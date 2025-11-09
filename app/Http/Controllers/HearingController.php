<?php

namespace App\Http\Controllers;

use App\Http\Requests\HearingRequest;
use App\Models\Hearing;
use App\Models\Organization;
use App\Models\Region;

class HearingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization)
    {
        $allHearings = $this->scopedHearingsQuery($organization)
            ->with(['organization', 'region'])
            ->get();

        $pendingHearings = collect();

        if (auth()->user()->is_admin || auth()->user()->is_superuser) {
            $pendingHearings = $allHearings
                ->filter(function ($hearing) {
                    return !$hearing->approved;
                })
                ->sortBy('start_datetime');
        }

        $approvedHearings = $allHearings->filter(function ($hearing) {
            return $hearing->approved;
        });
        
        // Split hearings into upcoming and past based on start_datetime
        $today = now()->startOfDay();
        $upcomingHearings = $approvedHearings->filter(function ($hearing) use ($today) {
            return $hearing->start_datetime && $hearing->start_datetime->gte($today);
        })->sortBy('start_datetime');
        
        $pastHearings = $approvedHearings->filter(function ($hearing) use ($today) {
            return $hearing->start_datetime && $hearing->start_datetime->lt($today);
        })->sortByDesc('start_datetime');
        
        return view('hearings.index', compact('pendingHearings', 'upcomingHearings', 'pastHearings'));
    }

    /**
     * Export hearings and related vote data as CSV.
     */
    public function export(?Organization $organization = null)
    {
        $hearings = $this->fullHearingsQuery($organization)->get();
        $columns = $this->getExportColumns();

        $filename = 'hearings-export-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($hearings, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($hearings as $hearing) {
                $rowData = $this->formatHearingRow($hearing);
                $orderedRow = array_map(function ($column) use ($rowData) {
                    return $rowData[$column] ?? '';
                }, $columns);

                fputcsv($handle, $orderedRow);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Render a compact, embed-friendly hearings table.
     */
    public function embed(?Organization $organization = null)
    {
        $hearings = $this->fullHearingsQuery($organization)->get();
        $columns = $this->getEmbedColumns();

        $rows = $hearings->map(function ($hearing) use ($columns) {
            $rowData = $this->formatHearingRow($hearing);

            return array_map(function ($column) use ($rowData) {
                return $rowData[$column] ?? '';
            }, $columns);
        });

        return view('hearings.embed', [
            'columns' => $columns,
            'rows' => $rows,
            'recordCount' => $rows->count(),
            'generatedAt' => now()->format('Y-m-d H:i:s T'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization)
    {
        $regions = Region::with('organization')
            ->where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();

        return view('hearings.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Organization $organization, HearingRequest $request)
    {
        $validated = $request->validated();

        $region = $this->findRegionForOrganization((int) ($validated['region_id'] ?? 0), $organization);

        // Extract datetime fields for conversion
        $startDate = $validated['start_date'];
        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];
        
        // Extract image file if present
        $imageFile = $request->file('image');
        
        // Debug logging
        if ($imageFile) {
            \Log::info('Image file received:', [
                'name' => $imageFile->getClientOriginalName(),
                'size' => $imageFile->getSize(),
                'mime' => $imageFile->getMimeType(),
                'extension' => $imageFile->getClientOriginalExtension()
            ]);
        } else {
            \Log::info('No image file received');
        }

        // Remove form-only fields before mass assignment
        unset($validated['start_date'], $validated['start_time'], $validated['end_time'], $validated['image'], $validated['organization_id']);

        // Create hearing with mass assignment scoped to the organization
        $hearing = new Hearing($validated);
        $hearing->organization_id = $organization->id;
        $hearing->region_id = $region->id;
        
        // Set datetime fields from form data
        $hearing->setDateTimeFromForm($startDate, $startTime, $endTime);
        
        // Keep development hearing title aligned with the street address
        if ($hearing->type === 'development') {
            $hearing->title = $hearing->street_address;
        }

        $hearing->approved = $request->boolean('approved', auth()->user()->is_admin || auth()->user()->is_superuser);
        
        // Save first to get an ID for the image filename
        $hearing->save();
        
        // Handle image upload after saving (needs the hearing ID)
        if ($imageFile) {
            try {
                $hearing->image_url = $hearing->handleImageUpload($imageFile, $hearing->id);
                $hearing->save(); // Save again with the image URL
            } catch (\Exception $e) {
                // Log the error but don't fail the entire operation
                \Log::error('Image upload failed for hearing ' . $hearing->id . ': ' . $e->getMessage());
                // Continue without the image - the hearing will still be created
            }
        }

        return redirect($this->orgRoute('hearings.index'))->with('success', 'Hearing created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, Hearing $hearing)
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);

        $hearing->load(['region', 'organization']);
        
        // Public hearing view - no additional access restrictions needed
        
        // Get list of users subscribed to receive notifications for this hearing (only for authenticated admins)
        $subscribedUsers = collect();
        $emailNotifications = collect();
        
        if (auth()->check() && (auth()->user()->is_admin || auth()->user()->is_superuser)) {
            $subscribedUsers = \App\Models\User::whereHas('regions', function ($query) use ($hearing) {
                $query->where('region_id', $hearing->region_id);
            })
            ->whereHas('notificationSettings', function ($query) use ($hearing) {
                if ($hearing->type === 'development') {
                    $query->where('notify_development_hearings', true);
                } else {
                    $query->where('notify_policy_hearings', true);
                }
            })
            ->with(['regions', 'notificationSettings'])
            ->orderBy('name')
            ->get();
            
            // Get email notifications for this hearing
            $emailNotifications = \App\Models\EmailNotification::where('hearing_id', $hearing->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('hearings.show', compact('hearing', 'subscribedUsers', 'emailNotifications'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, Hearing $hearing)
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);
        
        $regions = Region::with('organization')
            ->where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();

        return view('hearings.edit', compact('hearing', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Organization $organization, HearingRequest $request, Hearing $hearing)
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);
        
        $validated = $request->validated();

        $region = $this->findRegionForOrganization((int) ($validated['region_id'] ?? 0), $organization);

        // Extract datetime fields for conversion
        $startDate = $validated['start_date'];
        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];
        
        // Extract image file if present
        $imageFile = $request->file('image');
        
        // Remove form-only fields before mass assignment
        unset($validated['start_date'], $validated['start_time'], $validated['end_time'], $validated['image'], $validated['organization_id']);

        $hearing->fill($validated);
        $hearing->organization_id = $organization->id;
        $hearing->region_id = $region->id;

        // Keep development hearing title aligned with the street address
        if ($hearing->type === 'development') {
            $hearing->title = $hearing->street_address;
        }

        $hearing->approved = $request->boolean('approved', $hearing->approved);
        
        // Set datetime fields from form data
        $hearing->setDateTimeFromForm($startDate, $startTime, $endTime);
        
        // Handle image upload
        if ($imageFile) {
            try {
                // Delete old image if exists
                $hearing->deleteImage();
                
                // Upload new image
                $hearing->image_url = $hearing->handleImageUpload($imageFile, $hearing->id);
            } catch (\Exception $e) {
                // Log the error but don't fail the entire operation
                \Log::error('Image upload failed for hearing ' . $hearing->id . ': ' . $e->getMessage());
                // Continue without updating the image
            }
        }
        
        $hearing->save();

        return redirect($this->orgRoute('hearings.index'))->with('success', 'Hearing updated successfully!');
    }

    /**
     * Approve a pending hearing.
     */
    public function approve(Organization $organization, Hearing $hearing)
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);

        if (!$hearing->approved) {
            $hearing->approved = true;
            $hearing->save();
        }

        return redirect($this->orgRoute('hearings.index'))->with('success', 'Hearing approved successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, Hearing $hearing)
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);
        
        // Delete associated image file
        $hearing->deleteImage();
        
        $hearing->delete();

        return redirect($this->orgRoute('hearings.index'))->with('success', 'Hearing deleted successfully!');
    }

    /**
     * Get a query builder scoped to the authenticated user's hearing access.
     */
    private function scopedHearingsQuery(?Organization $organization = null)
    {
        $user = auth()->user();
        $organization = $organization ?: $this->currentOrganizationOrFail();
        $query = Hearing::query()->where('organization_id', $organization->id);

        if ($user->is_superuser || $user->is_admin) {
            return $query;
        }

        $monitoredRegionIds = $user->regions()->pluck('regions.id');

        return $query->whereIn('region_id', $monitoredRegionIds)
            ->where('approved', true);
    }

    /**
     * Base query for public exports and embeds.
     */
    private function fullHearingsQuery(?Organization $organization = null)
    {
        $query = Hearing::with([
            'organization',
            'region',
            'hearingVote.councillorVotes.councillor',
        ])->where('approved', true);

        if ($organization) {
            $query->where('organization_id', $organization->id);
        }

        return $query->orderByDesc('start_datetime');
    }

    private function ensureHearingBelongsToOrganization(Hearing $hearing, Organization $organization): void
    {
        if ($hearing->organization_id !== $organization->id) {
            abort(404);
        }
    }

    private function findRegionForOrganization(int $regionId, Organization $organization): Region
    {
        if ($regionId <= 0) {
            abort(422, 'A valid region is required.');
        }

        $region = Region::where('id', $regionId)
            ->where('organization_id', $organization->id)
            ->first();

        if (!$region) {
            abort(404);
        }

        return $region;
    }

    /**
     * Provide the canonical list of export columns.
     */
    private function getExportColumns(): array
    {
        return [
            'ID',
            'Organization',
            'Region',
            'Type',
            'Title',
            'Street Address',
            'Postal Code',
            'Rental',
            'Units',
            'Below Market Units',
            'Replaced Units',
            'Subject To Vote',
            'Description',
            'Image URL',
            'Start Datetime',
            'End Datetime',
            'More Info URL',
            'Remote Instructions',
            'In-Person Instructions',
            'Comments Email',
            'Created At',
            'Updated At',
            'Vote Date',
            'Vote Result',
            'Vote Passed',
            'Vote Notes',
            'Councillors In Favour',
            'Councillors Against',
            'Councillors Abstained',
            'Councillors Absent',
        ];
    }

    /**
     * Column set used for iframe embed.
     */
    private function getEmbedColumns(): array
    {
        return [
            'Start Datetime',
            'Title',
            'Postal Code',
            'Region',
            'Rental',
            'Units',
            'Below Market Units',
            'Replaced Units',
            'Subject To Vote',
            'Vote Date',
            'Vote Result',
            'Vote Passed',
            'Councillors In Favour',
            'Councillors Against',
            'Councillors Abstained',
            'Councillors Absent',
            'Vote Notes',
        ];
    }

    /**
     * Map a hearing model onto the export column set.
     */
    private function formatHearingRow(\App\Models\Hearing $hearing): array
    {
        $vote = $hearing->hearingVote;
        $councillorVotes = $vote ? $vote->councillorVotes : collect();

        $row = [
            'ID' => $hearing->id,
            'Organization' => optional($hearing->organization)->name,
            'Region' => optional($hearing->region)->name,
            'Type' => $hearing->type,
            'Title' => $hearing->title,
            'Street Address' => $hearing->street_address,
            'Postal Code' => $hearing->postal_code,
            'Rental' => $hearing->rental,
            'Units' => $hearing->units,
            'Below Market Units' => $hearing->below_market_units,
            'Replaced Units' => $hearing->replaced_units,
            'Subject To Vote' => $hearing->subject_to_vote,
            'Description' => $hearing->description,
            'Image URL' => $hearing->image_url,
            'Start Datetime' => $hearing->start_datetime,
            'End Datetime' => $hearing->end_datetime,
            'More Info URL' => $hearing->more_info_url,
            'Remote Instructions' => $hearing->remote_instructions,
            'In-Person Instructions' => $hearing->inperson_instructions,
            'Comments Email' => $hearing->comments_email,
            'Created At' => $hearing->created_at,
            'Updated At' => $hearing->updated_at,
            'Vote Date' => $vote && $vote->vote_date ? $vote->vote_date->format('Y-m-d') : '',
            'Vote Result' => $vote ? $vote->vote_result : '',
            'Vote Passed' => $vote && !is_null($vote->passed) ? ($vote->passed ? 'Yes' : 'No') : '',
            'Vote Notes' => $vote ? $vote->notes : '',
            'Councillors In Favour' => $this->formatCouncillorList($councillorVotes, 'for'),
            'Councillors Against' => $this->formatCouncillorList($councillorVotes, 'against'),
            'Councillors Abstained' => $this->formatCouncillorList($councillorVotes, 'abstain'),
            'Councillors Absent' => $this->formatCouncillorList($councillorVotes, 'absent'),
        ];

        foreach ($row as $key => $value) {
            $row[$key] = $this->normalizeTabularValue($value);
        }

        return $row;
    }

    /**
     * Build a semicolon-delimited councillor list for a given vote type.
     */
    private function formatCouncillorList($councillorVotes, string $type): string
    {
        return $councillorVotes
            ->where('vote', $type)
            ->map(function ($councillorVote) {
                return optional($councillorVote->councillor)->name;
            })
            ->filter()
            ->unique()
            ->values()
            ->implode('; ');
    }

    /**
     * Normalize tabular values to a single-line string output.
     */
    private function normalizeTabularValue($value): string
    {
        if (is_null($value)) {
            return '';
        }

        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        $string = (string) $value;
        $string = preg_replace('/\s+/', ' ', $string ?? '');

        return trim($string);
    }

    /**
     * Add hearing to calendar - redirect to calendar service
     */
    public function addToCalendar(Organization $organization, Hearing $hearing, $provider)
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);

        $url = $this->generateCalendarUrl($hearing, $provider);
        return redirect($url);
    }

    /**
     * Download ICS file for hearing
     */
    public function downloadIcs(Organization $organization, Hearing $hearing)
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);

        $icsContent = $this->generateIcsContent($hearing);
        $filename = 'hearing-' . $hearing->id . '.ics';
        
        return response($icsContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate calendar URL for different providers
     */
    private function generateCalendarUrl(\App\Models\Hearing $hearing, $provider)
    {
        $startDateTime = $this->getHearingDateTime($hearing, 'start');
        $endDateTime = $this->getHearingDateTime($hearing, 'end');
        $title = $hearing->display_title;
        $description = $this->formatHearingDescription($hearing);
        $location = $this->getHearingLocation($hearing);

        switch ($provider) {
            case 'google':
                return "https://calendar.google.com/calendar/render?" . http_build_query([
                    'action' => 'TEMPLATE',
                    'text' => $title,
                    'dates' => $startDateTime . '/' . $endDateTime,
                    'details' => $description,
                    'location' => $location,
                    'ctz' => config('app.timezone', 'UTC')
                ]);

            case 'outlook':
                return "https://outlook.live.com/calendar/0/deeplink/compose?" . http_build_query([
                    'subject' => $title,
                    'startdt' => \Carbon\Carbon::parse($startDateTime)->toISOString(),
                    'enddt' => \Carbon\Carbon::parse($endDateTime)->toISOString(),
                    'body' => $description,
                    'location' => $location
                ]);

            case 'yahoo':
                $duration = \Carbon\Carbon::parse($startDateTime)->diffInMinutes(\Carbon\Carbon::parse($endDateTime));
                $durationFormatted = sprintf('%02d%02d', floor($duration / 60), $duration % 60);
                
                return "https://calendar.yahoo.com/?" . http_build_query([
                    'v' => 60,
                    'title' => $title,
                    'st' => $startDateTime,
                    'dur' => $durationFormatted,
                    'desc' => $description,
                    'in_loc' => $location
                ]);

            default:
                abort(404, 'Calendar provider not supported');
        }
    }

    /**
     * Generate ICS file content
     */
    private function generateIcsContent(\App\Models\Hearing $hearing)
    {
        $startDateTime = $this->getHearingDateTime($hearing, 'start');
        $endDateTime = $this->getHearingDateTime($hearing, 'end');
        $title = $hearing->display_title;
        $description = $this->formatHearingDescription($hearing);
        $location = $this->getHearingLocation($hearing);
        $uid = 'hearing-' . $hearing->id . '@' . request()->getHost();
        $timestamp = now()->format('Ymd\THis\Z');

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Housing Alerts//Housing Alerts//EN\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:" . $uid . "\r\n";
        $ics .= "DTSTAMP:" . $timestamp . "\r\n";
        $ics .= "DTSTART:" . $startDateTime . "\r\n";
        $ics .= "DTEND:" . $endDateTime . "\r\n";
        $ics .= "SUMMARY:" . $this->escapeIcsText($title) . "\r\n";
        $ics .= "DESCRIPTION:" . $this->escapeIcsText($description) . "\r\n";
        $ics .= "LOCATION:" . $this->escapeIcsText($location) . "\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Get formatted datetime for hearing
     */
    private function getHearingDateTime(\App\Models\Hearing $hearing, $type = 'start')
    {
        if ($type === 'start' && $hearing->start_datetime) {
            return $hearing->start_datetime->utc()->format('Ymd\THis\Z');
        } elseif ($type === 'end' && $hearing->end_datetime) {
            return $hearing->end_datetime->utc()->format('Ymd\THis\Z');
        } else {
            // Default datetimes if not set
            $defaultStart = $hearing->start_datetime ?: now()->addHour()->setMinute(0)->setSecond(0);
            $defaultEnd = $hearing->end_datetime ?: $defaultStart->copy()->addHours(2);
            
            return ($type === 'start' ? $defaultStart : $defaultEnd)->utc()->format('Ymd\THis\Z');
        }
    }

    /**
     * Format hearing description for calendar
     */
    private function formatHearingDescription(\App\Models\Hearing $hearing)
    {
        $description = $hearing->description ?: '';
        
        // Add instructions for participation
        if ($hearing->remote_instructions || $hearing->inperson_instructions) {
            $description .= "\n\n--- PARTICIPATION INSTRUCTIONS ---";
            
            if ($hearing->remote_instructions) {
                $description .= "\n\nVIRTUAL PARTICIPATION:\n" . $hearing->remote_instructions;
            }
            
            if ($hearing->inperson_instructions) {
                $description .= "\n\nIN-PERSON PARTICIPATION:\n" . $hearing->inperson_instructions;
            }
        }
        
        if ($hearing->comments_email) {
            $description .= "\n\nCOMMENTS EMAIL: " . $hearing->comments_email;
        }
        
        if ($hearing->more_info_url) {
            $description .= "\n\nMORE INFORMATION: " . $hearing->more_info_url;
        }

        return trim($description);
    }

    /**
     * Get hearing location for calendar
     */
    private function getHearingLocation(\App\Models\Hearing $hearing)
    {
        // Return empty location - instructions will be in the description
        return '';
    }

    /**
     * Escape text for ICS format
     */
    private function escapeIcsText($text)
    {
        // First normalize line endings to \n
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        
        // Then escape special characters
        $text = str_replace(["\\", ",", ";"], ["\\\\", "\\,", "\\;"], $text);
        
        // Finally convert \n to proper ICS line breaks
        $text = str_replace("\n", "\\n", $text);
        
        return $text;
    }
}

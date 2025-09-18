<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HearingRequest;

class HearingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->is_superuser) {
            // Superusers can see all hearings across organizations
            $allHearings = \App\Models\Hearing::with(['organization', 'region'])->get();
        } elseif (auth()->user()->is_admin) {
            // Regular admins can only see hearings within their organization
            $allHearings = \App\Models\Hearing::with(['organization', 'region'])
                ->where('organization_id', auth()->user()->organization_id)
                ->get();
        } else {
            // Regular users can only see hearings in their monitored regions
            $monitoredRegionIds = auth()->user()->regions()->pluck('regions.id');
            $allHearings = \App\Models\Hearing::with(['organization', 'region'])
                ->whereIn('region_id', $monitoredRegionIds)
                ->get();
        }
        
        // Split hearings into upcoming and past based on start_datetime
        $today = now()->startOfDay();
        $upcomingHearings = $allHearings->filter(function ($hearing) use ($today) {
            return $hearing->start_datetime && $hearing->start_datetime->gte($today);
        })->sortBy('start_datetime');
        
        $pastHearings = $allHearings->filter(function ($hearing) use ($today) {
            return $hearing->start_datetime && $hearing->start_datetime->lt($today);
        })->sortByDesc('start_datetime');
        
        return view('hearings.index', compact('upcomingHearings', 'pastHearings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get regions based on user role
        if (auth()->user()->is_superuser) {
            // Superusers can see all regions across organizations
            $regions = \App\Models\Region::with('organization')->orderBy('name')->get();
        } else {
            // Regular admins can only see regions within their organization
            $regions = \App\Models\Region::where('organization_id', auth()->user()->organization_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('hearings.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HearingRequest $request)
    {
        $validated = $request->validated();

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
        unset($validated['start_date'], $validated['start_time'], $validated['end_time'], $validated['image']);

        // Create hearing with mass assignment
        $hearing = new \App\Models\Hearing($validated);
        
        // Set datetime fields from form data
        $hearing->setDateTimeFromForm($startDate, $startTime, $endTime);
        
        // Auto-generate title for development hearings to match the address
        if ($hearing->type === 'development' && empty($hearing->title)) {
            $hearing->title = "{$hearing->street_address}";
        }
        
        // Force organization_id to match the user's organization unless superuser
        if (!auth()->user()->is_superuser && $request->has('organization_id')) {
            $hearing->organization_id = auth()->user()->organization_id;
        } else if (auth()->user()->is_superuser && $request->has('organization_id')) {
            $hearing->organization_id = $request->organization_id;
        } else {
            $hearing->organization_id = auth()->user()->organization_id;
        }
        
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

        return redirect()->route('hearings.index')->with('success', 'Hearing created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hearing = \App\Models\Hearing::with(['region', 'organization'])->findOrFail($id);
        
        // Public hearing view - no access restrictions needed
        
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
    public function edit($id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        
        // Get regions based on user role
        if (auth()->user()->is_superuser) {
            // Superusers can see all regions across organizations
            $regions = \App\Models\Region::with('organization')->orderBy('name')->get();
        } else {
            // Regular admins can only see regions within their organization
            $regions = \App\Models\Region::where('organization_id', auth()->user()->organization_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('hearings.edit', compact('hearing', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HearingRequest $request, $id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        
        $validated = $request->validated();

        // Extract datetime fields for conversion
        $startDate = $validated['start_date'];
        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];
        
        // Extract image file if present
        $imageFile = $request->file('image');
        
        // Remove form-only fields before mass assignment
        unset($validated['start_date'], $validated['start_time'], $validated['end_time'], $validated['image']);

        $hearing->fill($validated);
        
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
        
        // Auto-generate title for development hearings if not provided
        if ($hearing->type === 'development' && empty($hearing->title)) {
            $hearing->title = "Hearing for {$hearing->street_address}";
        }
        
        $hearing->save();

        return redirect()->route('hearings.index')->with('success', 'Hearing updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        
        // Delete associated image file
        $hearing->deleteImage();
        
        $hearing->delete();
        return redirect()->route('hearings.index')->with('success', 'Hearing deleted successfully!');
    }

    /**
     * Add hearing to calendar - redirect to calendar service
     */
    public function addToCalendar(\App\Models\Hearing $hearing, $provider)
    {
        $url = $this->generateCalendarUrl($hearing, $provider);
        return redirect($url);
    }

    /**
     * Download ICS file for hearing
     */
    public function downloadIcs(\App\Models\Hearing $hearing)
    {
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

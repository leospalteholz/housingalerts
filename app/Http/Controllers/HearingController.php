<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        
        // Split hearings into upcoming and past based on start_date
        $today = now()->startOfDay();
        $upcomingHearings = $allHearings->filter(function ($hearing) use ($today) {
            return $hearing->start_date && \Carbon\Carbon::parse($hearing->start_date)->gte($today);
        })->sortBy('start_date');
        
        $pastHearings = $allHearings->filter(function ($hearing) use ($today) {
            return $hearing->start_date && \Carbon\Carbon::parse($hearing->start_date)->lt($today);
        })->sortByDesc('start_date');
        
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
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'type' => 'required|in:development,policy',
            'description' => 'required|string',
            'remote_instructions' => 'required|string',
            'inperson_instructions' => 'required|string',
            'comments_email' => 'required|email|max:255',
            'start_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'organization_id' => 'nullable|exists:organizations,id',
            'region_id' => 'nullable|exists:regions,id',
            'image_url' => 'nullable|string',
            'more_info_url' => 'nullable|url',
        ];

        // Add conditional validation based on hearing type
        if ($request->type === 'development') {
            $rules = array_merge($rules, [
                'street_address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'rental' => 'required|boolean',
                'units' => 'required|integer|min:1',
                'title' => 'nullable|string|max:255',
            ]);
        } else if ($request->type === 'policy') {
            $rules = array_merge($rules, [
                'title' => 'required|string|max:255',
                'street_address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'rental' => 'nullable|boolean',
                'units' => 'nullable|integer|min:1',
            ]);
        }

        $validated = $request->validate($rules);

        // Create a new hearing
        $hearing = new \App\Models\Hearing($validated);
        
        // Auto-generate title for development hearings if not provided
        if ($hearing->type === 'development' && empty($hearing->title)) {
            $hearing->title = "Hearing for {$hearing->street_address}";
        }
        
        // Force organization_id to match the user's organization unless superuser
        if (!auth()->user()->is_superuser && $request->has('organization_id')) {
            $hearing->organization_id = auth()->user()->organization_id;
        } else if (auth()->user()->is_superuser && $request->has('organization_id')) {
            $hearing->organization_id = $request->organization_id;
        } else {
            $hearing->organization_id = auth()->user()->organization_id;
        }
        
        $hearing->save();

        return redirect()->route('hearings.index')->with('success', 'Hearing created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        
        // Check access permissions
        if (auth()->user()->is_superuser) {
            // Superusers can view any hearing
        } elseif (auth()->user()->is_admin) {
            // Admins can only view hearings in their organization
            if ($hearing->organization_id !== auth()->user()->organization_id) {
                abort(403, 'You do not have permission to view this hearing.');
            }
        } else {
            // Regular users can only view hearings in their monitored regions
            $monitoredRegionIds = auth()->user()->regions()->pluck('regions.id');
            if (!$monitoredRegionIds->contains($hearing->region_id)) {
                abort(403, 'You can only view hearings in regions you are monitoring.');
            }
        }
        
        return view('hearings.show', compact('hearing'));
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
    public function update(Request $request, $id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        
        // Base validation rules
        $rules = [
            'type' => 'required|in:development,policy',
            'description' => 'required|string',
            'remote_instructions' => 'required|string',
            'inperson_instructions' => 'required|string',
            'comments_email' => 'required|email|max:255',
            'start_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'organization_id' => 'nullable|exists:organizations,id',
            'region_id' => 'nullable|exists:regions,id',
            'image_url' => 'nullable|string',
            'more_info_url' => 'nullable|url',
        ];

        // Add conditional validation based on hearing type
        if ($request->type === 'development') {
            $rules = array_merge($rules, [
                'street_address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'rental' => 'required|boolean',
                'units' => 'required|integer|min:1',
                'title' => 'nullable|string|max:255',
            ]);
        } else if ($request->type === 'policy') {
            $rules = array_merge($rules, [
                'title' => 'required|string|max:255',
                'street_address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'rental' => 'nullable|boolean',
                'units' => 'nullable|integer|min:1',
            ]);
        }

        $validated = $request->validate($rules);

        $hearing->fill($validated);
        
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
        if (!$hearing->start_date) {
            // Default to today if no date set
            $date = now()->format('Y-m-d');
        } else {
            $date = \Carbon\Carbon::parse($hearing->start_date)->format('Y-m-d');
        }

        if ($type === 'start' && $hearing->start_time) {
            $time = $hearing->start_time;
        } elseif ($type === 'end' && $hearing->end_time) {
            $time = $hearing->end_time;
        } else {
            // Default times
            $time = $type === 'start' ? '10:00:00' : '11:00:00';
        }

        return \Carbon\Carbon::parse($date . ' ' . $time)->utc()->format('Ymd\THis\Z');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
    protected $fillable = [
        'organization_id',
        'region_id',
        'type',
        'title',
        'street_address',
        'postal_code',
        'rental',
        'units',
        'description',
        'image_url',
        'start_datetime',
        'end_datetime',
        'more_info_url',
        'remote_instructions',
        'inperson_instructions',
        'comments_email',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'rental' => 'boolean',
    ];

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function region() {
        return $this->belongsTo(Region::class);
    }

    // Accessor methods for form compatibility
    public function getStartDateAttribute()
    {
        return $this->start_datetime ? $this->start_datetime->format('Y-m-d') : null;
    }

    public function getStartTimeAttribute()
    {
        return $this->start_datetime ? $this->start_datetime->format('H:i:s') : null;
    }

    public function getEndTimeAttribute()
    {
        return $this->end_datetime ? $this->end_datetime->format('H:i:s') : null;
    }

    // Method to set datetime fields from form data
    public function setDateTimeFromForm($startDate, $startTime, $endTime)
    {
        $this->start_datetime = \Carbon\Carbon::parse($startDate . ' ' . $startTime);
        $this->end_datetime = \Carbon\Carbon::parse($startDate . ' ' . $endTime);
    }

    // Helper method to get display title
    public function getDisplayTitleAttribute()
    {
        if ($this->type === 'policy') {
            return $this->title;
        } else {
            return $this->title ?: "Hearing for {$this->street_address}";
        }
    }

    // Helper method to get combined date/time for display
    public function getHearingDateAttribute()
    {
        return $this->start_datetime;
    }

    // Helper method to check if this is a development hearing
    public function isDevelopment()
    {
        return $this->type === 'development';
    }

    // Helper method to check if this is a policy hearing
    public function isPolicy()
    {
        return $this->type === 'policy';
    }

    /**
     * Generate ICS file content for this hearing
     */
    public function generateIcsContent()
    {
        $startDateTime = $this->getHearingDateTime('start');
        $endDateTime = $this->getHearingDateTime('end');
        $title = $this->display_title;
        $description = $this->formatHearingDescription();
        $location = $this->getHearingLocation();
        $uid = 'hearing-' . $this->id . '@' . (request() ? request()->getHost() : 'housingalerts.local');
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
    private function getHearingDateTime($type = 'start')
    {
        if ($type === 'start' && $this->start_datetime) {
            return $this->start_datetime->utc()->format('Ymd\THis\Z');
        } elseif ($type === 'end' && $this->end_datetime) {
            return $this->end_datetime->utc()->format('Ymd\THis\Z');
        } else {
            // Default datetimes if not set
            $defaultStart = $this->start_datetime ?: now()->addHour()->setMinute(0)->setSecond(0);
            $defaultEnd = $defaultStart->copy()->addHours(2);
            
            return ($type === 'start' ? $defaultStart : $defaultEnd)->utc()->format('Ymd\THis\Z');
        }
    }

    /**
     * Format hearing description for calendar
     */
    private function formatHearingDescription()
    {
        $desc = "Housing Hearing: " . $this->display_title . "\n\n";
        
        if ($this->description) {
            $desc .= $this->description . "\n\n";
        }
        
        $desc .= "Comments Email: " . $this->comments_email . "\n";
        
        if ($this->more_info_url) {
            $desc .= "More Info: " . $this->more_info_url . "\n";
        }
        
        if ($this->remote_instructions) {
            $desc .= "\nRemote Instructions:\n" . $this->remote_instructions . "\n";
        }
        
        if ($this->inperson_instructions) {
            $desc .= "\nIn-Person Instructions:\n" . $this->inperson_instructions . "\n";
        }
        
        return $desc;
    }

    /**
     * Get hearing location for calendar
     */
    private function getHearingLocation()
    {
        if ($this->street_address) {
            return $this->street_address . ($this->postal_code ? ', ' . $this->postal_code : '');
        }
        
        return $this->region ? $this->region->name : 'Location TBD';
    }

    /**
     * Escape text for ICS format
     */
    private function escapeIcsText($text)
    {
        $text = str_replace(["\r\n", "\r", "\n"], "\\n", $text);
        $text = str_replace([",", ";", "\\"], ["\\,", "\\;", "\\\\"], $text);
        return $text;
    }
}

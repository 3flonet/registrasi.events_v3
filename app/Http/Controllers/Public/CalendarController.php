<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Download ICS file for the event.
     */
    public function ical(Event $event)
    {
        $start = $event->start_date->format('Ymd\THis\Z');
        $end = ($event->end_date ?? $event->start_date->addHours(2))->format('Ymd\THis\Z');
        $summary = $event->name;
        $description = strip_tags($event->description);
        $location = is_array($event->venue) ? ($event->venue['name'] ?? 'TBA') : ($event->venue ?? 'TBA');
        $url = route('events.show', $event->slug);

        $icsContent = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PROID:-//Registrasi Events//NONSGML v1.0//EN',
            'BEGIN:VEVENT',
            "UID:" . md5($event->id . $event->created_at),
            "DTSTAMP:" . Carbon::now()->format('Ymd\THis\Z'),
            "DTSTART:{$start}",
            "DTEND:{$end}",
            "SUMMARY:{$summary}",
            "DESCRIPTION:{$description} \\n\\nMore info: {$url}",
            "LOCATION:{$location}",
            'END:VEVENT',
            'END:VCALENDAR'
        ];

        $content = implode("\r\n", $icsContent);

        return response($content)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $event->slug . '.ics"');
    }

    /**
     * Helper to get Google Calendar Link
     */
    public static function getGoogleLink(Event $event)
    {
        $start = $event->start_date->format('Ymd\THis\Z');
        $end = ($event->end_date ?? $event->start_date->addHours(2))->format('Ymd\THis\Z');
        $text = urlencode($event->name);
        $details = urlencode(strip_tags($event->description));
        $location = urlencode(is_array($event->venue) ? ($event->venue['name'] ?? 'TBA') : ($event->venue ?? 'TBA'));

        return "https://www.google.com/calendar/render?action=TEMPLATE&text={$text}&dates={$start}/{$end}&details={$details}&location={$location}";
    }

    /**
     * Helper to get Outlook Calendar Link
     */
    public static function getOutlookLink(Event $event)
    {
        $start = $event->start_date->format('Y-m-d\TH:i:s\Z');
        $end = ($event->end_date ?? $event->start_date->addHours(2))->format('Y-m-d\TH:i:s\Z');
        $subject = urlencode($event->name);
        $body = urlencode(strip_tags($event->description));
        $location = urlencode(is_array($event->venue) ? ($event->venue['name'] ?? 'TBA') : ($event->venue ?? 'TBA'));

        return "https://outlook.office.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent&subject={$subject}&body={$body}&location={$location}&startdt={$start}&enddt={$end}";
    }
}

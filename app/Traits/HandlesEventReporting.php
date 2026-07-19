<?php

namespace App\Traits;

use App\Models\Invitation;
use App\Models\CheckinLog;
use Illuminate\Support\Facades\DB;

trait HandlesEventReporting
{
    public $totalRegistrations;
    public $uniqueAttendees;
    public $dailyBreakdown = [];
    public $chartSeries = [];
    public $chartCategories = [];
    
    public $invitationStats = [
        'total' => 0,
        'sent' => 0,
        'confirmed' => 0,
        'represented' => 0,
        'declined' => 0,
        'pending' => 0,
        'response_rate' => 0,
    ];

    public $ticketDistribution = [];
    public $hourlyAttendance = [];
    public $conversionMetrics = [];
    public $attendanceTypeDistribution = [];

    public function calculateStats()
    {
        // Check if event has ended during live session
        if (property_exists($this, 'isLiveMode') && $this->isLiveMode && $this->event->end_date && $this->event->end_date->isPast()) {
            $this->isLiveMode = false;
        }

        // 1. Total Pendaftar
        $this->totalRegistrations = $this->event->registrations()->count();

        // 2. Total Hadir (Unik)
        $this->uniqueAttendees = $this->event->checkinLogs()
            ->distinct()
            ->count('registration_id');

        // 3. Rincian Harian
        $this->dailyBreakdown = $this->event->checkinLogs()
            ->selectRaw('DATE(checkin_time) as checkin_date, COUNT(*) as count')
            ->groupByRaw('DATE(checkin_time), registrations.event_id')
            ->orderBy('checkin_date', 'asc')
            ->get();

        $this->chartCategories = $this->dailyBreakdown->map(function ($item) {
            return \Carbon\Carbon::parse($item->checkin_date)->format('d M');
        })->all();

        $this->chartSeries = $this->dailyBreakdown->map(function ($item) {
            return $item->count;
        })->all();

        // 4. Statistik Undangan
        $invitations = Invitation::where('event_id', $this->event->id)->get();

        $this->invitationStats['total']       = $invitations->count();
        $this->invitationStats['sent']        = $invitations->where(fn($i) => $i->is_sent_email || $i->is_sent_whatsapp)->count();
        $this->invitationStats['confirmed']   = $invitations->where('status', 'confirmed')->count();
        $this->invitationStats['represented'] = $invitations->where('status', 'represented')->count();
        $this->invitationStats['declined']    = $invitations->where('status', 'declined')->count();
        $this->invitationStats['pending']     = $invitations->where('status', 'pending')->count();

        $totalResponded = $this->invitationStats['confirmed'] + $this->invitationStats['represented'] + $this->invitationStats['declined'];
        $this->invitationStats['response_rate'] = $this->invitationStats['total'] > 0
            ? round(($totalResponded / $this->invitationStats['total']) * 100, 1)
            : 0;

        // 5. Ticket Tier Distribution
        $this->ticketDistribution = $this->event->registrations()
            ->select('ticket_tier_id', DB::raw('count(*) as total'))
            ->groupBy('ticket_tier_id', 'registrations.event_id')
            ->with('ticketTier')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->ticketTier ? $item->ticketTier->name : 'General Admission',
                    'total' => $item->total,
                    'percentage' => $this->totalRegistrations > 0 ? round(($item->total / $this->totalRegistrations) * 100, 1) : 0
                ];
            });

        // 6. Hourly Attendance Trend
        $this->hourlyAttendance = $this->event->checkinLogs()
            ->select(DB::raw('HOUR(checkin_time) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('HOUR(checkin_time)'), 'registrations.event_id')
            ->orderBy('hour')
            ->get()
            ->mapWithKeys(fn($item) => [$item->hour => $item->total])
            ->toArray();

        // 7. Conversion Metrics
        $this->conversionMetrics = [
            'invited' => $this->invitationStats['total'],
            'registered' => $this->totalRegistrations,
            'attended' => $this->uniqueAttendees,
            'conversion_rate' => $this->totalRegistrations > 0 
                ? round(($this->uniqueAttendees / $this->totalRegistrations) * 100, 1) 
                : 0
        ];

        // 8. Attendance Type Distribution
        $this->attendanceTypeDistribution = $this->event->registrations()
            ->select('attendance_type', DB::raw('count(*) as total'))
            ->groupBy('attendance_type', 'registrations.event_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->attendance_type ?: 'In-Person',
                    'total' => $item->total,
                    'percentage' => $this->totalRegistrations > 0 ? round(($item->total / $this->totalRegistrations) * 100, 1) : 0
                ];
            });
    }

    public function downloadPdfReport()
    {
        $this->calculateStats();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.event-report', [
            'event' => $this->event,
            'conversionMetrics' => $this->conversionMetrics,
            'invitationStats' => $this->invitationStats,
            'ticketDistribution' => $this->ticketDistribution,
            'chartCategories' => $this->chartCategories,
            'chartSeries' => $this->chartSeries,
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions(['isRemoteEnabled' => true]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "report-{$this->event->slug}.pdf");
    }
}

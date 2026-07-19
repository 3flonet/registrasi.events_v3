<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\Event;
use App\Models\Registration;
use App\Models\CheckinLog;
use App\Traits\HandlesCheckin;
use Livewire\Component;

class HandheldScanner extends Component
{
    use HandlesCheckin;
    public Event $event;
    public $manualUuid = '';
    public $lastScanned = [];
    public $totalRegistrants = 0;
    public $totalCheckedIn = 0;
    public $selectedSessionId = '';

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->totalRegistrants = $this->event->registrations()->count();
        $this->totalCheckedIn = CheckinLog::whereHas('registration', function($q) {
            $q->where('event_id', $this->event->id);
        })->whereBetween('checkin_time', [today()->startOfDay(), today()->endOfDay()])->distinct('registration_id')->count();
    }

    public function checkIn()
    {
        if (empty($this->manualUuid)) return;

        $uuid = basename($this->manualUuid);
        $registration = null;

        // 1. Scan TIKET
        $registration = Registration::where('uuid', $uuid)
            ->where('event_id', $this->event->id)
            ->first();

        // 2. Scan PROFIL
        if (!$registration) {
            $user = \App\Models\User::where('uuid', $uuid)->first();
            if ($user) {
                $registration = Registration::where('user_id', $user->id)
                    ->where('event_id', $this->event->id)
                    ->first();
            }
        }

        // 3. Validasi
        if (!$registration) {
            $this->lastScanned = ['status' => 'error', 'message' => 'Identity not found or unauthorized for this event ecosystem.'];
            $this->reset('manualUuid');
            $this->dispatch('refocus-scanner-input');
            return;
        }

        // LOGIKA TERPUSAT: Buat entri baru di checkin_logs & Kirim Notifikasi
        try {
            $sessionVal = $this->selectedSessionId ?: null;
            if ($this->performCheckIn($registration, $this->event, $sessionVal)) {
                 $this->lastScanned = ['status' => 'success', 'message' => 'Authorization Confirmed! Welcome, ' . $registration->name];
            } else {
                 $this->lastScanned = ['status' => 'warning', 'message' => $registration->name . ' has already authorized entry today.'];
                 $this->reset('manualUuid');
                 $this->dispatch('refocus-scanner-input');
                 return;
            }
        } catch (\Exception $e) {
             $this->lastScanned = ['status' => 'error', 'message' => $e->getMessage()];
             $this->reset('manualUuid');
             $this->dispatch('refocus-scanner-input');
             return;
        }
        
        $this->loadStats();
        $this->reset('manualUuid');
        $this->dispatch('refocus-scanner-input');
    }

    public function render()
    {
        return view('livewire.admin.checkin.handheld-scanner')->layout('layouts.app');
    }
}

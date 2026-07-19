<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Models\CheckinLog;
use App\Traits\HandlesCheckin;
use Livewire\Component;

class RfidTap extends Component
{
    use HandlesCheckin;

    public Event $event;
    public string $rfidTag = '';
    public array $lastStatus = [];
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
        })->whereDate('checkin_time', today())->distinct('registration_id')->count();
    }

    public function checkInByRfid()
    {
        $this->validate(['rfidTag' => 'required']);

        $user = User::where('rfid_tag', $this->rfidTag)->first();

        if (!$user) {
            $this->lastStatus = ['status' => 'error', 'message' => 'PHYSICAL TOKEN NOT RECOGNIZED.'];
            $this->dispatch('scan-processed');
            $this->reset('rfidTag');
            $this->dispatch('refocus-rfid-input');
            return;
        }

        $registration = Registration::where('user_id', $user->id)
            ->where('event_id', $this->event->id)
            ->first();

        if (!$registration) {
            $this->lastStatus = [
                'status' => 'error',
                'message' => 'UNAUTHORIZED: ' . $user->name . ' is not associated with this ecosystem.',
                'data' => [
                    'Identity Node' => $user->name,
                    'Email Protocol' => $user->email,
                ]
            ];
            $this->dispatch('scan-processed');
            $this->reset('rfidTag');
            $this->dispatch('refocus-rfid-input');
            return;
        }

        $participantData = [
            'Narrative Identity' => $registration->name,
            'Email Node' => $user->email,
            'Phone Signal' => $user->phone_number ?? '-',
            'Access Tier' => $registration->attendance_type ? ucwords($registration->attendance_type) : 'Standard',
        ];

        try {
            $sessionVal = $this->selectedSessionId ?: null;
            if ($this->performCheckIn($registration, $this->event, $sessionVal)) {
                $this->lastStatus = [
                    'status' => 'success',
                    'message' => 'SYNTHESIS COMPLETE! AUTH-LOG AUTHORIZED FOR: ' . $registration->name,
                    'data' => $participantData
                ];
            } else {
                $this->lastStatus = [
                    'status' => 'warning',
                    'message' => 'DUPLICATE: ' . $registration->name . ' is already synchronized for today.',
                    'data' => $participantData
                ];
                $this->dispatch('scan-processed');
                $this->reset('rfidTag');
                $this->dispatch('refocus-rfid-input');
                return;
            }
        } catch (\Exception $e) {
            $this->lastStatus = [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => $participantData
            ];
            $this->dispatch('scan-processed');
            $this->reset('rfidTag');
            $this->dispatch('refocus-rfid-input');
            return;
        }
        
        $this->loadStats();
        $this->dispatch('scan-processed');
        $this->reset('rfidTag');
        $this->dispatch('refocus-rfid-input');
    }
    
    public function resetStatus()
    {
        $this->lastStatus = [];
    }

    public function render()
    {
        return view('livewire.admin.checkin.rfid-tap')->layout('layouts.app');
    }
}

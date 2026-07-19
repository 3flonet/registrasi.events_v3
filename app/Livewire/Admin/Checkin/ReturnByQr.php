<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use Livewire\Component;

class ReturnByQr extends Component
{
    public Event $event;
    public ?User $selectedUser = null;
    public array $lastScanned = [];
    public string $manualUuid = '';
    public $totalRegistrants = 0;
    public $totalReturned = 0;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->totalRegistrants = $this->event->registrations()->count();
        // Returned is basically those who checked in but no longer have rfid_tag? 
        // Or we can track it better if we have a flag. 
        // For now, let's just count registrations for this event where the USER currently has NO rfid_tag but DID checkin.
        $this->totalReturned = Registration::where('event_id', $this->event->id)
            ->whereHas('checkinLogs')
            ->whereHas('user', function($q) {
                $q->whereNull('rfid_tag');
            })->count();
    }

    public function findUserManually()
    {
        $this->findUserByUuid(basename($this->manualUuid));
        $this->reset('manualUuid');
    }

    public function findUserByUuid($inputValue)
    {
        if (empty($inputValue)) return;

        $uuid = basename($inputValue);
        $user = User::where('uuid', $uuid)->first();
        
        if (!$user) {
            $registration = Registration::where('uuid', $uuid)->first();
            if ($registration) {
                if ($registration->user) {
                    $user = $registration->user;
                } else {
                    try {
                        $user = User::where('email', $registration->email)->first();
                        if (!$user) {
                            $user = User::create([
                                'name' => $registration->name,
                                'email' => $registration->email,
                                'phone_number' => $registration->phone_number,
                                'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                            ]);
                            $user->assignRole('Attendee');
                        }
                        $registration->update(['user_id' => $user->id]);
                    } catch (\Exception $e) {
                        $this->lastScanned = ['status' => 'error', 'message' => 'Profile synthesis failed: ' . $e->getMessage()];
                        $this->dispatch('reset-scanner-view');
                        return;
                    }
                }
            }
        }

        if (!$user) {
            $this->lastScanned = ['status' => 'error', 'message' => 'Identity not found in global roster.'];
            $this->dispatch('reset-scanner-view');
            return;
        }

        if (!$user->rfid_tag) {
            $this->lastScanned = ['status' => 'warning', 'message' => $user->name . ' has no physical token associated for reclamation.'];
            $this->dispatch('reset-scanner-view');
            return;
        }

        $this->selectedUser = $user;
        $this->lastScanned = ['status' => 'info', 'message' => 'Identity synthesized: ' . $user->name . '. Token: ' . $user->rfid_tag];
        $this->dispatch('open-return-modal');
    }

    public function confirmReturn()
    {
        if (!$this->selectedUser) {
            $this->lastScanned = ['status' => 'error', 'message' => 'No active subject selected for reclamation.'];
            return;
        }

        try {
            $this->selectedUser->update(['rfid_tag' => null]);
            $this->lastScanned = ['status' => 'success', 'message' => 'RECLAMATION AUTHORIZED! Token for ' . $this->selectedUser->name . ' has been decoupled.'];

            $this->loadStats();
            $this->reset('selectedUser');
            $this->dispatch('close-return-modal');
            $this->dispatch('reset-scanner-view');

        } catch (\Exception $e) {
            $this->lastScanned = ['status' => 'error', 'message' => 'PROTOCOL FAILURE! Error during token reclamation.'];
            $this->dispatch('close-return-modal');
            $this->dispatch('reset-scanner-view');
        }
    }

    public function render()
    {
        return view('livewire.admin.checkin.return-by-qr')->layout('layouts.app');
    }
}
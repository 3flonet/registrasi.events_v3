<?php

namespace App\Livewire\Public\Event;

use App\Models\Event;
use Livewire\Component;
use App\Traits\HandlesEventReporting;

use App\Models\Invitation;
use Livewire\WithPagination;

class LiveReport extends Component
{
    use HandlesEventReporting, WithPagination;

    public Event $event;
    public bool $isLiveMode = true;
    // public $participants = []; // Moved to render() to avoid large property serialization
    public $customFields = [];
    
    // Invitation filtering
    public $invitationSearch = '';
    public $invitationStatus = 'all';

    public function mount(Event $event)
    {
        $this->event = $event;

        // Auto-disable Live Mode if event already ended
        if ($this->event->end_date && $this->event->end_date->isPast()) {
            $this->isLiveMode = false;
        }

        // Ambil data kustom fields untuk header tabel
        if ($this->event->inquiryForm) {
            $this->customFields = $this->event->inquiryForm->fields;
        }

        $this->calculateStats();
        // $this->loadParticipants(); // No longer needed here
    }

    public function loadParticipants()
    {
        $this->participants = $this->event->registrations()
            ->with(['submission.media', 'ticketTier'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        $invitations = Invitation::where('event_id', $this->event->id)
            ->when($this->invitationSearch, function ($q) {
                $q->where(function($q) {
                    $q->where('name', 'like', '%' . $this->invitationSearch . '%')
                      ->orWhere('email', 'like', '%' . $this->invitationSearch . '%')
                      ->orWhere('company', 'like', '%' . $this->invitationSearch . '%');
                });
            })
            ->when($this->invitationStatus !== 'all', function ($q) {
                $q->where('status', $this->invitationStatus);
            })
            ->latest()
            ->paginate(15, pageName: 'invitation-page');

        $participants = $this->event->registrations()
            ->with(['submission.media', 'ticketTier'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.public.event.live-report', [
            'invitations' => $invitations,
            'participants' => $participants
        ])->layout('layouts.report'); // Use dedicated clean layout for public report
    }
}

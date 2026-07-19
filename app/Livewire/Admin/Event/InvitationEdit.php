<?php

namespace App\Livewire\Admin\Event;

use App\Models\Invitation;
use App\Models\Event;
use App\Models\Registration;
use Livewire\Component;

class InvitationEdit extends Component
{
    public $event;
    public $invitation;
    public $name;
    public $email;
    public $phone_number;
    public $company;
    public $category;

    public function mount(Event $event, Invitation $invitation)
    {
        $this->event = $event;
        $this->invitation = $invitation;
        $this->name = $invitation->name;
        $this->email = $invitation->email;
        $this->phone_number = $invitation->phone_number;
        $this->company = $invitation->company;
        $this->category = $invitation->category;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $this->invitation->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'company' => $this->company,
            'category' => $this->category,
        ]);

        // Cek jika ada registrasi terkait dan update namanya juga agar sinkron
        $registration = Registration::where('event_id', $this->event->id)
            ->where('email', $this->invitation->email)
            ->first();
            
        if ($registration) {
            $registration->update(['name' => $this->name]);
        }

        session()->flash('message', 'Guest profile architecture updated.');
        return $this->redirect(route('admin.events.invitations', $this->event), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.event.invitation-edit')
            ->layout('layouts.app');
    }
}

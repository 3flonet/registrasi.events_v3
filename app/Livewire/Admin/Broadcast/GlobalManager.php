<?php

namespace App\Livewire\Admin\Broadcast;

use App\Models\EventEmailTemplate;
use App\Models\Registration;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;
use App\Mail\GlobalBroadcastMail;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendGlobalBroadcastJob;
use Illuminate\Support\Facades\Queue;
use App\Models\PendingBroadcast;


class GlobalManager extends Component
{
    use WithFileUploads, WithPagination;

    // Properti untuk modal pengiriman tes
    public $showTestSendModal = false;
    public $templateForTestId;
    public $testEmail;
    public $totalRecipients = 0;
    public $showSendModal = false;
    public $templateForBroadcastId;
    public $broadcastType = 'email'; // Default ke email
    public $broadcastTarget = 'attendees'; // Default ke attendees
    public $confirmingDeletionId = null; // State untuk modal hapus
    public $search;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'content' => 'required|string',
        'banner' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->testEmail = auth()->user()->email;
        $this->totalRecipients = $this->calculateTotalRecipients();
    }

    public function updatedBroadcastType()
    {
        $this->totalRecipients = $this->calculateTotalRecipients();
    }

    public function updatedBroadcastTarget()
    {
        $this->totalRecipients = $this->calculateTotalRecipients();
    }

    public function calculateTotalRecipients()
    {
        if ($this->broadcastTarget === 'organizers') {
            if ($this->broadcastType === 'whatsapp') {
                return \App\Models\Organizer::whereNotNull('phone')->where('phone', '!=', '')->distinct()->count('phone');
            }
            return \App\Models\Organizer::whereNotNull('email')->where('email', '!=', '')->distinct()->count('email');
        } else {
            if ($this->broadcastType === 'whatsapp') {
                return \App\Models\Registration::whereNotNull('phone_number')->where('phone_number', '!=', '')->distinct()->count('phone_number');
            }
            return \App\Models\Registration::distinct()->count('email');
        }
    }

    // Logic for create and edit has move to TemplateForm component

    // Logic for save has move to TemplateForm component

    public function confirmDelete($templateId)
    {
        $this->confirmingDeletionId = $templateId;
    }

    public function delete()
    {
        $template = EventEmailTemplate::findOrFail($this->confirmingDeletionId);
        if ($template->banner_path) {
            Storage::disk('public')->delete($template->banner_path);
        }
        $template->delete();
        $this->confirmingDeletionId = null;
        session()->flash('message', 'Template global berhasil dihapus.');
    }

    public function openTestSendModal($templateId)
    {
        $this->templateForTestId = $templateId;
        $this->showTestSendModal = true;
    }

    public function openSendModal($templateId)
    {
        $this->templateForBroadcastId = $templateId;
        $this->showSendModal = true;
        $this->totalRecipients = $this->calculateTotalRecipients(); // Recalculate just in case
    }

    public function sendTestEmail()
    {
        $this->validate(['testEmail' => 'required|email']);

        $template = EventEmailTemplate::find($this->templateForTestId);
        if (!$template) {
            session()->flash('error', 'Template tidak ditemukan.');
            return;
        }

        $testRecipient = (object)[
            'name' => auth()->user()->name,
            'email' => $this->testEmail,
        ];
        Mail::to($this->testEmail)->queue(new GlobalBroadcastMail($template, $testRecipient));

        $this->showTestSendModal = false;
        session()->flash('message', 'Email tes berhasil dikirim ke ' . $this->testEmail);
    }

    public function confirmAndSendBroadcast()
    {
        $total = $this->calculateTotalRecipients();

        PendingBroadcast::create([
            'template_id' => $this->templateForBroadcastId,
            'status' => 'pending',
            'type' => $this->broadcastType,
            'target' => $this->broadcastTarget,
            'total_count' => $total,
        ]);

        session()->flash('message', 'Permintaan broadcast ' . strtoupper($this->broadcastType) . ' (' . strtoupper($this->broadcastTarget) . ') telah dicatat.');
        
        $this->showSendModal = false;
        $this->templateForBroadcastId = null;
    }

    // Logic for close and reset has move to TemplateForm component

    public function render()
    {
        $templates = EventEmailTemplate::whereNull('event_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        $broadcastHistory = PendingBroadcast::with('template')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'broadcastPage');

        $totalAttendees = \App\Models\Registration::distinct()->count('email');
        $totalOrganizers = \App\Models\Organizer::count();
    
        return view('livewire.admin.broadcast.global-manager', [
            'templates' => $templates,
            'broadcastHistory' => $broadcastHistory,
            'totalAttendees' => $totalAttendees,
            'totalOrganizers' => $totalOrganizers,
        ])->layout('layouts.app');
    }
}

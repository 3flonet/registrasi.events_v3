<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\InquiryForm;
use App\Models\FeedbackForm;
use App\Models\EventEmailTemplate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;
    
    public $search = '';
    public $showFeedbackModal = false;
    public $selectedEventIdForFeedback = null;
    public $feedback_form_id_to_assign = null;
    public $allFeedbackForms;
    public bool $brandingWarning = false;
    
    // Delete Modal Properties
    public bool $showDeleteModal = false;
    public ?int $deletingEventId = null;
    
    // Cancellation Properties
    public $showCancelModal = false;
    public $selectedEventIdForCancel = null;
    public $selectedTemplateId = null;
    public $cancellationTemplates = [];
    public $shouldNotifyParticipants = true;
    public $broadcastType = 'email'; // 'email' or 'whatsapp'
    // Transfer Properties
    public $showTransferModal = false;
    public $eventToTransfer = null;
    public $targetOrganizerId = null;
    public $organizerSearch = '';

    public $stats = [
        'events' => [
            'used' => 0,
            'limit' => -1,
            'percentage' => 0,
            'color' => 'teal'
        ],
        'registrants' => [
            'used' => 0,
            'limit' => -1,
            'percentage' => 0,
            'color' => 'indigo'
        ]
    ];

    public function mount()
    {
        $this->calculateStats();
        $this->checkBrandingStatus();
    }

    private function checkBrandingStatus()
    {
        $user = auth()->user();
        if ($user->isSuperAdmin() || !$user->organizer_id) return;

        $hasEmail = \App\Models\Setting::withoutGlobalScopes()
            ->where('organizer_id', $user->organizer_id)
            ->where('key', 'mail_host')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->exists();

        if (!$hasEmail) {
            $this->brandingWarning = true;
        }
    }

    private function calculateStats()
    {
        $organizer = auth()->user()->organizer;
        if (!$organizer) {
            // Jika bukan organizer (misal Super Admin), ambil total global tanpa limit
            $this->stats['events']['used'] = Event::count();
            $this->stats['registrants']['used'] = \App\Models\Registration::count();
            return;
        }

        $plan = $organizer->subscriptionPlan;
        $eventCount = Event::count();
        $registrantCount = \App\Models\Registration::count();

        $this->stats = [
            'events' => [
                'used' => $eventCount,
                'limit' => $plan ? $plan->event_limit : -1,
                'percentage' => $this->calculatePercentage($eventCount, $plan ? $plan->event_limit : -1),
                'color' => 'teal'
            ],
            'registrants' => [
                'used' => $registrantCount,
                'limit' => $plan ? $plan->registrant_limit : -1,
                'percentage' => $this->calculatePercentage($registrantCount, $plan ? $plan->registrant_limit : -1),
                'color' => 'indigo'
            ]
        ];
    }

    private function calculatePercentage($used, $limit)
    {
        if ((int)$limit === -1) return 0;
        if ((int)$limit === 0) return 100;
        return min(round(($used / (int)$limit) * 100), 100);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleStatus($eventId)
    {
        $event = Event::findOrFail($eventId);
        // Jika sedang cancelled, biarkan statusnya tetap atau atur ulang jika diaktifkan kembali
        if ($event->status === 'cancelled' && !$event->is_active) {
            $event->status = 'upcoming'; // Reset jika diaktifkan kembali
        }
        $event->is_active = !$event->is_active;
        $event->save();
        $this->dispatch('notify', 'Event status updated successfully.');
    }

    public function checkEventLimit()
    {
        $limit = (int)$this->stats['events']['limit'];
        $used = (int)$this->stats['events']['used'];

        if ($limit !== -1 && $used >= $limit) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'QUOTA REACHED',
                'text' => 'You have reached the maximum number of events allowed by your current plan (' . $limit . '). Please upgrade your plan to create more.',
                'showCancelButton' => true,
                'confirmButtonText' => 'Upgrade Now',
                'cancelButtonText' => 'Close',
                'confirmButtonColor' => '#1a1235',
                'cancelButtonColor' => '#ef4444',
                'reverseButtons' => true,
                'redirect' => route('admin.billing.index')
            ]);
            return;
        }

        return $this->redirect(route('admin.events.create'), navigate: true);
    }

    public function openCancelModal($eventId)
    {
        $this->selectedEventIdForCancel = $eventId;
        $this->cancellationTemplates = EventEmailTemplate::where('category', 'event_cancellation')->get();
        if ($this->cancellationTemplates->count() > 0) {
            $this->selectedTemplateId = $this->cancellationTemplates->first()->id;
        }
        $this->showCancelModal = true;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->reset(['selectedEventIdForCancel', 'selectedTemplateId', 'shouldNotifyParticipants']);
    }

    public function executeCancel()
    {
        $event = Event::findOrFail($this->selectedEventIdForCancel);
        $event->status = 'cancelled';
        $event->is_active = false; // Otomatis nonaktifkan
        $event->save();

        if ($this->shouldNotifyParticipants && $this->selectedTemplateId) {
            $totalRecipients = $event->registrations()->count();
            if ($totalRecipients > 0) {
                \App\Models\PendingEventBroadcast::create([
                    'event_id' => $event->id,
                    'template_id' => $this->selectedTemplateId,
                    'status' => 'pending',
                    'type' => $this->broadcastType,
                    'total_recipients' => $totalRecipients,
                ]);
                $this->dispatch('notify', 'Event cancelled and notifications queued.');
            } else {
                $this->dispatch('notify', 'Event cancelled (no participants to notify).');
            }
        } else {
            $this->dispatch('notify', 'Event cancelled successfully.');
        }

        $this->closeCancelModal();
    }

    public function toggleFeedbackStatus($eventId)
    {
        $event = Event::findOrFail($eventId);
        $event->is_feedback_active = !$event->is_feedback_active;
        $event->save();
        $this->dispatch('notify', 'Feedback status updated.');
    }

    public function openFeedbackFormModal($eventId)
    {
        $this->selectedEventIdForFeedback = $eventId;
        $event = Event::findOrFail($eventId);
        $this->feedback_form_id_to_assign = $event->feedback_form_id;
        $this->allFeedbackForms = FeedbackForm::where('organizer_id', auth()->user()->organizer_id)->get();
        $this->showFeedbackModal = true;
    }

    public function closeFeedbackFormModal()
    {
        $this->showFeedbackModal = false;
        $this->reset(['selectedEventIdForFeedback', 'feedback_form_id_to_assign']);
    }

    public function assignFeedbackForm()
    {
        $this->validate(['feedback_form_id_to_assign' => 'required|exists:feedback_forms,id']);
        $event = Event::findOrFail($this->selectedEventIdForFeedback);
        $event->update(['feedback_form_id' => $this->feedback_form_id_to_assign]);
        $this->closeFeedbackFormModal();
        $this->dispatch('notify', 'Feedback form assigned.');
    }

    public function confirmDelete($id)
    {
        $this->deletingEventId = $id;
        $this->showDeleteModal = true;
    }

    public function openTransferModal($eventId)
    {
        $this->eventToTransfer = Event::findOrFail($eventId);
        $this->showTransferModal = true;
    }

    public function closeTransferModal()
    {
        $this->showTransferModal = false;
        $this->reset(['eventToTransfer', 'targetOrganizerId', 'organizerSearch']);
    }

    public function transferEvent()
    {
        if (!auth()->user()->is_super_admin) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'UNAUTHORIZED', 'text' => 'Only Super Admins can transfer events.']);
            return;
        }

        $this->validate([
            'targetOrganizerId' => 'required|exists:organizers,id'
        ]);

        $this->eventToTransfer->update([
            'organizer_id' => $this->targetOrganizerId
        ]);

        $this->closeTransferModal();
        $this->dispatch('swal:success', [
            'title' => 'EVENT TRANSFERRED',
            'text' => 'The event has been successfully moved to the target organizer.',
        ]);
    }

    public function delete()
    {
        if ($this->deletingEventId) {
            $event = Event::findOrFail($this->deletingEventId);
            $event->delete();
            $this->showDeleteModal = false;
            $this->deletingEventId = null;
            $this->calculateStats();
            $this->dispatch('swal:success', [
                'title' => 'Event Deleted!',
                'text' => 'The event and all associated data have been permanently removed.',
            ]);
        }
    }

    public function render()
    {
        $events = Event::withCount([
            'registrations',
            'checkinLogs as today_checkins_count' => function ($query) {
                $query->whereDate('checkin_time', today());
            }
        ])
        ->with('feedbackForm')
        ->where(function ($query) {
            $searchTerm = '%' . strtolower($this->search) . '%';
            $query->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.en"))) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, "$.id"))) LIKE ?', [$searchTerm]);
        })
        ->latest()
        ->paginate(10);

        $organizers = [];
        if ($this->showTransferModal) {
            $organizers = \App\Models\Organizer::where('name', 'like', '%' . $this->organizerSearch . '%')
                ->where('id', '!=', $this->eventToTransfer?->organizer_id)
                ->limit(10)
                ->get();
        }

        return view('livewire.admin.event.index', [
            'events' => $events,
            'organizers' => $organizers
        ])
            ->layout('layouts.app');
    }
}

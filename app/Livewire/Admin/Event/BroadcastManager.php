<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\EventEmailTemplate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicBroadcastMail;
use App\Models\PendingEventBroadcast;
use App\Models\MessageTemplateCategory;

class BroadcastManager extends Component
{
    use WithFileUploads;

    public Event $event;
    public $templates;
    public $categories;

    public $isConfiguring = false;
    public $isBroadcasting = false;
    public $showDeleteModal = false;
    public $deletingTemplateId;
    public $editingTemplateId;
    public $subject;
    public $content = '';
    public $whatsapp_content = '';
    public $banner;
    public $existingBannerPath;
    public $category = 'broadcast';

    // Properti untuk pengiriman
    public $templateToSend;
    public $sendTarget = 'test'; // 'test' atau 'all'
    public $testEmail;
    public $testPhone;
    public $broadcastType = 'email';
    public $is_global = false;

    // Kita hilangkan property $rules dan memindahkannya ke dalam method save()
    // untuk menghindari error property not found pada beberapa versi Livewire 3

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->testEmail = auth()->user()->email; 
        $this->testPhone = auth()->user()->phone_number ?? '';
        $this->categories = MessageTemplateCategory::all();
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $this->templates = EventEmailTemplate::where(function($q) {
                $q->where('event_id', $this->event->id)
                  ->orWhereNull('event_id');
            })
            ->orderBy('event_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->isConfiguring = true;
        $this->isBroadcasting = false;
        $this->dispatch('init-editor');
    }

    public function edit($templateId)
    {
        $template = EventEmailTemplate::findOrFail($templateId);
        $this->editingTemplateId = $template->id;
        $this->subject = $template->subject;
        $this->content = $template->content;
        $this->whatsapp_content = $template->whatsapp_content;
        $this->existingBannerPath = $template->banner_path;
        $this->category = $template->category ?? 'broadcast';
        $this->is_global = is_null($template->event_id);

        $this->isConfiguring = true;
        $this->isBroadcasting = false;
        $this->dispatch('set-content', content: $this->content);
    }

    public function save()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'whatsapp_content' => 'nullable|string',
            'category' => 'required|string',
            'banner' => 'nullable|image|max:2048',
        ]);

        $data = [
            'event_id' => $this->is_global ? null : $this->event->id,
            'subject' => $this->subject,
            'content' => $this->content,
            'whatsapp_content' => $this->whatsapp_content,
            'category' => $this->category,
        ];

        if ($this->banner) {
            if ($this->editingTemplateId && $this->existingBannerPath) {
                Storage::disk('public')->delete($this->existingBannerPath);
            }
            $data['banner_path'] = $this->banner->store('email-banners', 'public');
        }

        EventEmailTemplate::updateOrCreate(['id' => $this->editingTemplateId], $data);

        session()->flash('message', 'Template email successfully architecturalized.');
        $this->closeConfig();
    }

    public function confirmDelete($templateId)
    {
        $this->deletingTemplateId = $templateId;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deletingTemplateId) {
            $template = EventEmailTemplate::findOrFail($this->deletingTemplateId);
            if ($template->banner_path) {
                Storage::disk('public')->delete($template->banner_path);
            }
            $template->delete();

            $this->showDeleteModal = false;
            $this->deletingTemplateId = null;
            $this->loadTemplates();

            $this->dispatch('swal:success', [
                'title' => 'Template Deleted!',
                'text' => 'The message template has been successfully obliterated.',
            ]);
        }
    }

    public function openBroadcast($templateId)
    {
        $this->templateToSend = EventEmailTemplate::findOrFail($templateId);
        $this->broadcastType = 'email'; // Reset ke default email setiap buka modal
        $this->isBroadcasting = true;
        $this->isConfiguring = false;
    }

    public function initiateBroadcast()
    {
        if (!$this->templateToSend) {
            session()->flash('error', 'Target template not identified.');
            return;
        }

        if ($this->sendTarget === 'test') {
            $firstRegistration = $this->event->registrations()->first();
            if (!$firstRegistration) {
                session()->flash('error', 'No registration samples found for transmission testing.');
                return;
            }

            if ($this->broadcastType === 'email') {
                $this->validate(['testEmail' => 'required|email']);
                Mail::to($this->testEmail)->queue(new DynamicBroadcastMail($this->templateToSend, $firstRegistration));
                session()->flash('message', 'Test Email transmission executed to ' . $this->testEmail);
            } else {
                $this->validate(['testPhone' => 'required']);
                
                try {
                    $whatsapp = new \App\Services\WhatsAppService($this->event->organizer_id);
                    $parser = app(\App\Services\MessageParserService::class);
                    $parsedData = $parser->parseForWhatsApp($this->templateToSend->whatsapp_content, $firstRegistration, $this->templateToSend);
                    
                    if ($parsedData['attachment_url']) {
                        $response = $whatsapp->sendFile($this->testPhone, $parsedData['message'], $parsedData['attachment_url'], $parsedData['fallback_url']);
                    } else {
                        $response = $whatsapp->sendMessage($this->testPhone, $parsedData['message']);
                    }

                    if (isset($response['status']) && $response['status'] == true) {
                        session()->flash('message', 'Test WhatsApp transmission successful to ' . $this->testPhone);
                    } else {
                        session()->flash('error', 'WhatsApp API Failure: ' . ($response['reason'] ?? 'Unknown Error'));
                    }
                } catch (\Exception $e) {
                    session()->flash('error', 'WhatsApp Test Exception: ' . $e->getMessage());
                }
            }
            
        } elseif ($this->sendTarget === 'all') {
            $query = $this->event->registrations();
            
            // Filtering: Jika kategori adalah feedback atau certificate, hanya kirim ke yang sudah hadir
            if (in_array($this->templateToSend->category, ['feedback', 'certificate'])) {
                $query->whereNotNull('checked_in_at');
            }
            
            $totalRecipients = $query->count();

            if ($totalRecipients === 0) {
                $msg = in_array($this->templateToSend->category, ['feedback', 'certificate']) 
                    ? 'No attended participants detected for this category.' 
                    : 'Zero recipients detected in this ecosystem.';
                session()->flash('error', $msg);
                return;
            }

            PendingEventBroadcast::create([
                'event_id' => $this->event->id,
                'template_id' => $this->templateToSend->id,
                'status' => 'pending',
                'type' => $this->broadcastType,
                'total_recipients' => $totalRecipients,
            ]);

            session()->flash('message', 'Mass ' . strtoupper($this->broadcastType) . ' broadcast authorized for ' . $totalRecipients . ' nodes. Processing via background queue.');
        }

        $this->isBroadcasting = false;
    }

    public function closeConfig()
    {
        $this->isConfiguring = false;
        $this->resetForm();
        $this->loadTemplates();
    }

    public function cancelBroadcast()
    {
        $this->isBroadcasting = false;
        $this->templateToSend = null;
    }

    private function resetForm()
    {
        $this->editingTemplateId = null;
        $this->subject = '';
        $this->content = '';
        $this->whatsapp_content = '';
        $this->banner = null;
        $this->existingBannerPath = null;
        $this->is_global = false;
        $this->category = 'broadcast';
    }

    public function render()
    {
        $broadcastHistory = PendingEventBroadcast::with('template')
            ->where('event_id', $this->event->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'broadcastPage');

        return view('livewire.admin.event.broadcast-manager', [
            'broadcastHistory' => $broadcastHistory,
            'availableCategories' => $this->categories,
        ])->layout('layouts.app');
    }
}

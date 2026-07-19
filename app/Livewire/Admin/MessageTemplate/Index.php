<?php

namespace App\Livewire\Admin\MessageTemplate;

use App\Models\EventEmailTemplate;
use App\Models\MessageTemplateCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';
    public bool $showTestModal = false;
    public ?int $testingTemplateId = null;
    public string $testPhone = '';
    public string $testEmail = '';
    public string $testMode = 'physical'; // physical or virtual
    public bool $showDeleteModal = false;
    public ?int $deletingTemplateId = null;

    public function openTestModal($id)
    {
        $this->testingTemplateId = $id;
        $this->testPhone = auth()->user()->phone ?? '';
        $this->testEmail = auth()->user()->email ?? '';
        $this->showTestModal = true;
    }

    public function sendTest(\App\Services\MessageParserService $parser, \App\Services\WhatsAppService $waService)
    {
        $this->validate([
            'testPhone' => 'required',
            'testEmail' => 'required|email',
        ]);

        $template = EventEmailTemplate::find($this->testingTemplateId);
        if (!$template) return;

        // 1. Ambil data pendaftaran asli terakhir agar link-link berfungsi (jika ada)
        $realRegistration = \App\Models\Registration::latest()->first();

        if ($realRegistration) {
            $registration = $realRegistration;
            // Tetap gunakan email/phone pengetesan
            $registration->email = $this->testEmail;
            $registration->phone_number = $this->testPhone;
        } else {
            // Fallback ke dummy jika database benar-benar kosong
            $registration = new \App\Models\Registration([
                'name' => auth()->user()->name ?? 'Demo User',
                'email' => $this->testEmail,
                'phone_number' => $this->testPhone,
                'attendance_type' => $this->testMode === 'physical' ? 'offline' : 'online',
                'total_price' => 150000,
            ]);
            $registration->id = rand(999, 9999);
            $registration->uuid = (string) \Illuminate\Support\Str::uuid();
        }

        // Mock Event relation jika belum ada
        if (!$registration->event) {
            $event = new \App\Models\Event([
                'name' => 'Demo Spectacular 2024',
                'type' => $this->testMode === 'physical' ? 'offline' : 'online',
                'venue' => 'Grand Ballroom, Jakarta Hilton',
                'platform' => 'Zoom Premium',
                'meeting_link' => 'https://zoom.us/j/demo123',
                'start_date' => now()->addDays(7),
                'is_feedback_active' => true,
            ]);
            $event->id = rand(100, 999);
            $event->uuid = (string) \Illuminate\Support\Str::uuid();
            $registration->setRelation('event', $event);
        }

        // 2. Parse Content
        $parsedEmail = $parser->parse($template->content ?? '', $registration, $template);

        try {
            // 3. Send Test WhatsApp
            if ($template->whatsapp_template_id && $template->whatsappTemplate) {
                $whatsappTemplate = $template->whatsappTemplate;
                
                $bodyParams = [];
                if (isset($whatsappTemplate->parameters['body'])) {
                    $bodyParams = $parser->parseParameters($whatsappTemplate->parameters['body'], $registration);
                }

                $headerParam = null;
                if (isset($whatsappTemplate->parameters['header']) && $whatsappTemplate->parameters['header']) {
                    $headerType = $whatsappTemplate->parameters['header']['type'];
                    $headerKey = $whatsappTemplate->parameters['header']['value'];
                    $resolvedVal = $parser->parseParameters([$headerKey], $registration)[0] ?? null;
                    if ($resolvedVal) {
                        $headerParam = [
                            'type' => $headerType,
                            'value' => $resolvedVal,
                            'filename' => 'Tiket.pdf'
                        ];
                    }
                }

                $buttonParams = [];
                if (isset($whatsappTemplate->parameters['buttons'])) {
                    foreach ($whatsappTemplate->parameters['buttons'] as $btn) {
                        $resolvedBtnVal = $parser->parseParameters([$btn['value']], $registration)[0] ?? null;
                        if ($resolvedBtnVal) {
                            if ($btn['value'] === 'ticket_url' || $btn['value'] === 'payment_link') {
                                $resolvedBtnVal = $registration->uuid;
                            }
                            $buttonParams[] = [
                                'index' => $btn['index'],
                                'value' => $resolvedBtnVal
                            ];
                        }
                    }
                }

                $payloadParams = [
                    'header' => $headerParam,
                    'body' => $bodyParams,
                    'buttons' => $buttonParams
                ];

                $waService->sendTemplateMessage(
                    $this->testPhone,
                    $whatsappTemplate->name,
                    $whatsappTemplate->language_code,
                    $payloadParams
                );
            } elseif ($template->whatsapp_content) {
                $parsedWA = $parser->parse($template->whatsapp_content ?? '', $registration, $template);
                $waService->sendMessage($this->testPhone, $parsedWA);
            }

            // 4. Send Test Email
            if ($template->content) {
                \Illuminate\Support\Facades\Mail::html($parsedEmail, function ($message) use ($template) {
                    $message->to($this->testEmail)
                            ->subject('[TEST] ' . $template->subject);
                });
            }

            $this->dispatch('swal:success', [
                'title' => 'Test Sent!',
                'text' => 'The test message has been dispatched to ' . $this->testPhone . ' and ' . $this->testEmail,
            ]);

            $this->showTestModal = false;
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Test Failed!',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $templates = EventEmailTemplate::query()
            ->whereNull('event_id')
            ->when($this->search, fn($q) =>
                $q->where('subject', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterCategory, fn($q) =>
                $q->where('category', $this->filterCategory)
            )
            ->latest()
            ->paginate(12);

        $categories = MessageTemplateCategory::orderByRaw("CASE WHEN slug = 'others' THEN 1 ELSE 0 END ASC")
            ->orderBy('id', 'ASC')
            ->get();

        return view('livewire.admin.message-template.index', compact('templates', 'categories'))
            ->layout('layouts.app');
    }

    public function confirmDelete($id)
    {
        $this->deletingTemplateId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deletingTemplateId) {
            $template = EventEmailTemplate::find($this->deletingTemplateId);
            if ($template) {
                $template->delete();
                $this->dispatch('swal:success', [
                    'title' => 'Template Deleted!',
                    'text' => 'The global message template has been successfully obliterated.',
                ]);
            }
            $this->showDeleteModal = false;
            $this->deletingTemplateId = null;
        }
    }

    #[On('delete-template')]
    public function destroy($templateId)
    {
        $template = EventEmailTemplate::find($templateId);

        if (!$template) {
            $this->dispatch('delete-failed', message: 'Template not found.');
            return;
        }

        $template->delete();
        $this->dispatch('template-deleted', message: 'Template deleted successfully.');
    }
}

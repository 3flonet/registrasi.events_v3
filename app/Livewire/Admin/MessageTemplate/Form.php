<?php

namespace App\Livewire\Admin\MessageTemplate;

use App\Models\EventEmailTemplate;
use App\Models\MessageTemplateCategory;
use App\Models\WhatsAppTemplate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?int $templateId = null;
    public string $subject = '';
    public string $content = '';
    public string $whatsapp_content = '';
    public ?int $whatsapp_template_id = null;
    public array $buttons_mapping = [];

    // Custom Header Media for WhatsApp (Organizer-level overrides)
    public $whatsapp_header_file;
    public ?string $existingWhatsappHeaderPath = null;

    // Test fields
    public string $testPhone = '';

    #[Url]
    public string $category = 'transactional';
    public $banner;
    public ?string $existingBannerPath = null;
    public bool $isRawHtml = false;
    public string $type = 'both'; // 'email', 'whatsapp', 'both'

    public array $categoryOptions = [];
    public $whatsappTemplates = [];

    public function mount($template = null)
    {
        $this->categoryOptions = MessageTemplateCategory::orderByRaw("CASE WHEN slug = 'others' THEN 1 ELSE 0 END ASC")
            ->orderBy('id', 'ASC')
            ->pluck('name', 'slug')
            ->toArray();
        // Capture category from URL if present (redundancy for #[Url])
        $queryCategory = request()->query('category');
        if ($queryCategory && in_array($queryCategory, ['transactional', 'auto_checkin', 'event_invoice'])) {
            $this->category = $queryCategory;
        }

        $this->loadWhatsAppTemplates();

        if ($template) {
            $model = EventEmailTemplate::findOrFail($template);

            // Security: Organizer can only edit their own templates
            if (!auth()->user()->isSuperAdmin() && $model->organizer_id !== auth()->user()->organizer_id) {
                abort(403);
            }

            $this->templateId          = $model->id;
            $this->subject             = $model->subject ?? '';
            $this->content             = $model->content ?? '';
            $this->whatsapp_content    = $model->whatsapp_content ?? '';
            $this->whatsapp_template_id = $model->whatsapp_template_id;
            $this->category            = $model->category ?? 'transactional';
            $this->existingBannerPath  = $model->banner_path;
            $this->existingWhatsappHeaderPath = $model->whatsapp_header_media_path;

            $this->buttons_mapping     = $model->whatsapp_buttons_mapping ?? [];

            if ($this->content && ($this->whatsapp_content || $this->whatsapp_template_id)) {
                $this->type = 'both';
            } elseif ($this->whatsapp_content || $this->whatsapp_template_id) {
                $this->type = 'whatsapp';
            } else {
                $this->type = 'email';
            }
        }
        $this->initializeButtonsMapping();
    }

    public function updatedCategory()
    {
        $this->loadWhatsAppTemplates();
    }

    public function updatedWhatsappTemplateId()
    {
        $this->initializeButtonsMapping();
    }

    protected function initializeButtonsMapping()
    {
        if (!$this->whatsapp_template_id) {
            $this->buttons_mapping = [];
            return;
        }

        $tpl = WhatsAppTemplate::find($this->whatsapp_template_id);
        if (!$tpl) {
            $this->buttons_mapping = [];
            return;
        }

        $params = $tpl->parameters ?? [];
        $buttons = $params['buttons'] ?? [];

        // Check if we are editing and it matches the current template
        $existingMapping = [];
        if ($this->templateId) {
            $model = EventEmailTemplate::find($this->templateId);
            if ($model && $model->whatsapp_template_id == $this->whatsapp_template_id) {
                $existingMapping = $model->whatsapp_buttons_mapping ?? [];
            }
        }

        $newMapping = [];
        foreach ($buttons as $btn) {
            if (($btn['type'] ?? '') === 'url' && ($btn['url_type'] ?? '') === 'dynamic') {
                $idx = $btn['index'];
                // Set default mapping to what's defined in the template if not set in model
                $newMapping[$idx] = $existingMapping[$idx] ?? ($btn['value'] ?? 'ticket_url');
            }
        }
        $this->buttons_mapping = $newMapping;
    }

    protected function loadWhatsAppTemplates()
    {
        $this->whatsappTemplates = WhatsAppTemplate::where('category', $this->category)
            ->where('is_active', true)
            ->get();
    }

    public function sendTestWhatsApp()
    {
        $this->validate([
            'testPhone' => 'required',
            'whatsapp_template_id' => 'required|exists:whatsapp_templates,id'
        ], [
            'testPhone.required' => 'Nomor HP tujuan uji coba wajib diisi.',
            'whatsapp_template_id.required' => 'Pilih template WhatsApp terlebih dahulu.'
        ]);

        $tpl = WhatsAppTemplate::find($this->whatsapp_template_id);
        $ws = app(\App\Services\WhatsAppService::class);

        // Map mock/test values
        $bodyValues = [];
        if (isset($tpl->parameters['body'])) {
            foreach ($tpl->parameters['body'] as $param) {
                $bodyValues[] = match($param) {
                    'name' => 'Budi Santoso',
                    'event_name' => 'Seminar Teknologi Nasional 2026',
                    'ticket_code' => 'REG-12345',
                    'event_instruction' => 'Silakan datang langsung ke Auditorium Utama lantai 3.',
                    'date' => '25 Okt 2026',
                    'time' => '09:00',
                    'total_bayar' => 'Rp 150.000',
                    default => 'Pengujian'
                };
            }
        }

        $headerVal = null;
        if (isset($tpl->parameters['header']) && $tpl->parameters['header']) {
            $headerVal = [
                'type' => $tpl->parameters['header']['type'],
                'value' => $tpl->parameters['header']['type'] === 'document' 
                    ? 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf' 
                    : 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500',
                'filename' => 'Tiket_Test.pdf'
            ];
        }

        $buttonVals = [];
        if (isset($tpl->parameters['buttons'])) {
            foreach ($tpl->parameters['buttons'] as $btn) {
                $buttonVals[] = [
                    'index' => $btn['index'],
                    'value' => 'test-uuid-12345'
                ];
            }
        }

        $payloadParams = [
            'header' => $headerVal,
            'body' => $bodyValues,
            'buttons' => $buttonVals
        ];

        $res = $ws->sendTemplateMessage($this->testPhone, $tpl->name, $tpl->language_code, $payloadParams);

        if ($res['status']) {
            session()->flash('wa_test_success', 'WhatsApp uji coba berhasil dikirim!');
        } else {
            session()->flash('wa_test_error', 'Gagal mengirim: ' . ($res['reason'] ?? 'Unknown Error'));
        }
    }

    public function save()
    {
        $rules = [
            'subject'               => 'required|string|max:255',
            'content'               => 'nullable|string',
            'whatsapp_template_id'  => 'nullable|exists:whatsapp_templates,id',
            'category'              => 'required|in:' . implode(',', array_keys($this->categoryOptions)),
            'banner'                => 'nullable|image|max:2048',
            'whatsapp_header_file'  => 'nullable',
        ];

        if ($this->whatsapp_template_id) {
            $tpl = \App\Models\WhatsAppTemplate::find($this->whatsapp_template_id);
            if ($tpl && isset($tpl->parameters['header']['type'])) {
                $headerType = $tpl->parameters['header']['type'];
                if ($headerType === 'image') {
                    $rules['whatsapp_header_file'] = 'nullable|image|max:5120';
                } elseif ($headerType === 'video') {
                    $rules['whatsapp_header_file'] = 'nullable|mimetypes:video/mp4|max:16384';
                } elseif ($headerType === 'document') {
                    $rules['whatsapp_header_file'] = 'nullable|mimes:pdf|max:10240';
                }
            }
        }

        $this->validate($rules, [
            'subject.required'  => 'Template name / subject is required.',
            'category.required' => 'Please select a category.',
            'category.in'       => 'Invalid category selected.',
            'whatsapp_header_file.image' => 'Header file must be an image.',
            'whatsapp_header_file.mimes' => 'Header file must be a PDF document.',
            'whatsapp_header_file.mimetypes' => 'Header file must be an MP4 video.',
        ]);

        $data = [
            'event_id'             => null,
            'subject'              => $this->subject,
            'content'              => $this->content,
            'whatsapp_template_id' => $this->whatsapp_template_id,
            'category'             => $this->category,
            'whatsapp_buttons_mapping' => $this->whatsapp_template_id ? $this->buttons_mapping : null,
        ];

        // Assign organizer_id for non-super-admin
        if (!auth()->user()->isSuperAdmin()) {
            $data['organizer_id'] = auth()->user()->organizer_id;
        }

        if ($this->banner) {
            if ($this->templateId && $this->existingBannerPath) {
                Storage::disk('public')->delete($this->existingBannerPath);
            }
            $data['banner_path'] = $this->banner->store('email-banners', 'public');
        }

        if ($this->whatsapp_header_file) {
            if ($this->templateId && $this->existingWhatsappHeaderPath) {
                Storage::disk('public')->delete($this->existingWhatsappHeaderPath);
            }
            $data['whatsapp_header_media_path'] = $this->whatsapp_header_file->store('event-whatsapp-headers', 'public');
        }

        EventEmailTemplate::updateOrCreate(['id' => $this->templateId], $data);

        session()->flash('message', $this->templateId
            ? 'Template updated successfully.'
            : 'New template created successfully.'
        );

        return redirect()->route('admin.message-templates.index');
    }

    public function render()
    {
        return view('livewire.admin.message-template.form')
            ->layout('layouts.app');
    }
}

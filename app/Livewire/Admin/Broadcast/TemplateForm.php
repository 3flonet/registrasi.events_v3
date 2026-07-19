<?php

namespace App\Livewire\Admin\Broadcast;

use App\Models\EventEmailTemplate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class TemplateForm extends Component
{
    use WithFileUploads;

    public $templateId;
    public $subject;
    public $content;
    public $category = 'broadcast';
    public $banner;
    public $existingBannerPath;
    public $whatsapp_content = '';
    public ?int $whatsapp_template_id = null;
    public array $buttons_mapping = [];

    // Custom Header Media for WhatsApp (Organizer-level overrides)
    public $whatsapp_header_file;
    public ?string $existingWhatsappHeaderPath = null;

    public $whatsappTemplates = [];
    public string $type = 'both'; // 'email', 'whatsapp', 'both'

    public function mount($template = null)
    {
        $this->loadWhatsAppTemplates();

        if ($template) {
            $templateModel = EventEmailTemplate::findOrFail($template);
            $this->templateId = $templateModel->id;
            $this->subject = $templateModel->subject;
            $this->content = $templateModel->content;
            $this->whatsapp_content = $templateModel->whatsapp_content;
            $this->category = 'broadcast';
            $this->existingBannerPath = $templateModel->banner_path;
            $this->whatsapp_template_id = $templateModel->whatsapp_template_id;
            $this->existingWhatsappHeaderPath = $templateModel->whatsapp_header_media_path;
            $this->buttons_mapping = $templateModel->whatsapp_buttons_mapping ?? [];

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

        $tpl = \App\Models\WhatsAppTemplate::find($this->whatsapp_template_id);
        if (!$tpl) {
            $this->buttons_mapping = [];
            return;
        }

        $params = $tpl->parameters ?? [];
        $buttons = $params['buttons'] ?? [];

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
                $newMapping[$idx] = $existingMapping[$idx] ?? ($btn['value'] ?? 'ticket_url');
            }
        }
        $this->buttons_mapping = $newMapping;
    }

    protected function loadWhatsAppTemplates()
    {
        $this->whatsappTemplates = \App\Models\WhatsAppTemplate::where('category', 'broadcast')
            ->where('is_active', true)
            ->get();
    }

    public function save()
    {
        $rules = [
            'subject' => 'required|string|max:255',
            'content' => 'nullable|string',
            'whatsapp_template_id' => 'nullable|exists:whatsapp_templates,id',
            'banner' => 'nullable|image|max:2048',
            'whatsapp_header_file' => 'nullable',
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
            'whatsapp_header_file.image' => 'Header file must be an image.',
            'whatsapp_header_file.mimes' => 'Header file must be a PDF document.',
            'whatsapp_header_file.mimetypes' => 'Header file must be an MP4 video.',
        ]);

        $data = [
            'event_id' => null, // Global Template
            'subject' => $this->subject,
            'content' => $this->content,
            'whatsapp_content' => $this->whatsapp_content,
            'whatsapp_template_id' => $this->whatsapp_template_id,
            'whatsapp_buttons_mapping' => $this->whatsapp_template_id ? $this->buttons_mapping : null,
            'category' => 'broadcast',
        ];

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

        session()->flash('message', 'Template broadcast berhasil disimpan.');
        return redirect()->route('admin.global-broadcast');
    }

    public function render()
    {
        return view('livewire.admin.broadcast.template-form')->layout('layouts.app');
    }
}

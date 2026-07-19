<?php

namespace App\Livewire\Admin\WhatsAppTemplate;

use App\Models\WhatsAppTemplate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?int $templateId = null;
    public string $name = '';
    public string $category = 'transactional';
    public string $meta_category = 'UTILITY';
    public string $language_code = 'id';
    public string $body_preview = '';
    public bool $is_active = true;

    // Header parameters configuration (none, document, image, video)
    public string $header_component_type = 'none';
    public $header_file;
    public ?string $existing_header_url = null;

    // Footer parameters configuration
    public string $footer_text = '';

    // Body parameters configuration (stored as comma-separated in text area and parsed)
    public string $body_params_text = 'name, event_name, ticket_code';

    // Buttons parameters configuration (stored as JSON/array structure)
    public array $buttons = [];

    public function mount($template = null)
    {
        if ($template) {
            $model = WhatsAppTemplate::findOrFail($template);
            $this->templateId = $model->id;
            $this->name = $model->name;
            $this->category = $model->category;
            $this->meta_category = $model->meta_category ?? 'UTILITY';
            $this->language_code = $model->language_code;
            $this->body_preview = $model->body_preview ?? '';
            $this->is_active = $model->is_active;

            $params = $model->parameters;
            
            // Header
            if (isset($params['header']) && $params['header']) {
                $this->header_component_type = $params['header']['type'] ?? 'none';
                if (in_array($this->header_component_type, ['image', 'video'])) {
                    $rawUrl = $params['header']['value'] ?? null;
                    $this->existing_header_url = \App\Services\WhatsAppService::resolveMediaUrl($rawUrl);
                }
            } else {
                $this->header_component_type = 'none';
            }

            // Footer
            if (isset($params['footer']) && is_array($params['footer'])) {
                $this->footer_text = $params['footer']['text'] ?? '';
            }

            // Body
            if (isset($params['body']) && is_array($params['body'])) {
                $this->body_params_text = implode(', ', $params['body']);
            }

            // Buttons
            if (isset($params['buttons']) && is_array($params['buttons'])) {
                $this->buttons = $params['buttons'];
                foreach ($this->buttons as $idx => $btn) {
                    if (($btn['type'] ?? '') === 'url' && ($btn['url_type'] ?? '') === 'whatsapp_me') {
                        $url = $btn['static_url'] ?? '';
                        if (str_starts_with($url, 'https://wa.me/')) {
                            $this->buttons[$idx]['static_url'] = substr($url, strlen('https://wa.me/'));
                        }
                    }
                }
            }
        }
    }

    public function addQuickReplyButton()
    {
        if (count($this->buttons) >= 10) {
            return;
        }
        $this->buttons[] = [
            'index' => count($this->buttons),
            'type' => 'quick_reply',
            'text' => 'Saya Hadir',
            'value' => null
        ];
    }

    public function addCtaButton()
    {
        if (count($this->buttons) >= 10) {
            return;
        }
        $this->buttons[] = [
            'index' => count($this->buttons),
            'type' => 'url',
            'text' => 'Kunjungi Website',
            'url_type' => 'static', // static or dynamic
            'value' => 'ticket_url'
        ];
    }

    public function removeButton($index)
    {
        unset($this->buttons[$index]);
        $this->buttons = array_values($this->buttons);
        // Re-index
        foreach ($this->buttons as $idx => $btn) {
            $this->buttons[$idx]['index'] = $idx;
        }
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:whatsapp_templates,name,' . ($this->templateId ?: 'NULL'),
            'category' => 'required|string|max:50',
            'language_code' => 'required|string|max:10',
            'body_preview' => 'nullable|string',
        ];

        // Validation for new uploads of image/video if no existing url
        if (in_array($this->header_component_type, ['image', 'video'])) {
            if (!$this->existing_header_url) {
                $rules['header_file'] = 'required|file|mimes:jpeg,png,jpg,mp4|max:10240'; // max 10MB
            } else {
                $rules['header_file'] = 'nullable|file|mimes:jpeg,png,jpg,mp4|max:10240';
            }
        }

        $this->validate($rules, [
            'name.required' => 'Nama template Meta wajib diisi.',
            'name.unique' => 'Nama template ini sudah terdaftar di database.',
            'header_file.required' => 'File media (gambar/video) wajib diunggah untuk tipe header ini.',
            'header_file.mimes' => 'Format file harus berupa jpeg, png, jpg, atau mp4.',
            'header_file.max' => 'Ukuran file maksimal adalah 10MB.'
        ]);

        // Process body params
        $bodyParams = array_map('trim', explode(',', $this->body_params_text));
        $bodyParams = array_filter($bodyParams);

        // Process header params
        $headerParam = null;
        if ($this->header_component_type !== 'none') {
            if ($this->header_component_type === 'document') {
                $headerParam = [
                    'type' => 'document',
                    'value' => 'ticket_pdf'
                ];
            } else {
                // Image or Video (Static Uploads)
                $value = $this->existing_header_url;
                if ($this->header_file) {
                    $path = $this->header_file->store('whatsapp_templates', 'public');
                    $value = asset('storage/' . $path);
                }
                $headerParam = [
                    'type' => $this->header_component_type,
                    'value' => $value
                ];
            }
        }

        // Process footer params
        $footerParam = null;
        if (!empty($this->footer_text)) {
            $footerParam = [
                'text' => $this->footer_text
            ];
        }

        $processedButtons = $this->buttons;
        foreach ($processedButtons as $idx => $btn) {
            if (($btn['type'] ?? '') === 'url' && ($btn['url_type'] ?? '') === 'whatsapp_me') {
                $phone = preg_replace('/[^0-9]/', '', $btn['static_url'] ?? '');
                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                }
                $processedButtons[$idx]['static_url'] = 'https://wa.me/' . $phone;
            }
        }

        $parameters = [
            'header' => $headerParam,
            'body' => array_values($bodyParams),
            'footer' => $footerParam,
            'buttons' => array_values($processedButtons)
        ];

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'meta_category' => $this->meta_category,
            'language_code' => $this->language_code,
            'body_preview' => $this->body_preview,
            'parameters' => $parameters,
            'is_active' => $this->is_active,
        ];

        if (!$this->templateId) {
            $data['meta_status'] = 'DRAFT';
        }

        WhatsAppTemplate::updateOrCreate(['id' => $this->templateId], $data);

        session()->flash('message', $this->templateId 
            ? 'WhatsApp Template updated successfully.' 
            : 'New WhatsApp Template created successfully.'
        );

        return redirect()->route('admin.whatsapp-templates.index');
    }

    public function render()
    {
        return view('livewire.admin.whatsapp-template.form')
            ->layout('layouts.app');
    }
}

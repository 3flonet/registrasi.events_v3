<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CertificateConfig extends Component
{
    use WithFileUploads;

    public Event $event;
    
    // Config Properties
    public $is_active = false;
    public $bg_image;
    public $signature_image;
    public $title = 'Certificate of Participation';
    public $body = 'is hereby granted this certificate for successfully participating in the';
    public $signer_name = '';
    public $signer_title = '';
    
    // Existing paths
    public $existing_bg_url;
    public $existing_signature_url;

    public function mount(Event $event)
    {
        $this->event = $event;
        $config = $event->certificate_config ?? [];
        
        $this->is_active = $config['is_active'] ?? false;
        $this->title = $config['title'] ?? 'Certificate of Participation';
        $this->body = $config['body'] ?? 'is hereby granted this certificate for successfully participating in the';
        $this->signer_name = $config['signer_name'] ?? '';
        $this->signer_title = $config['signer_title'] ?? '';
        
        $this->existing_bg_url = isset($config['bg_path']) ? Storage::disk('public')->url($config['bg_path']) : null;
        $this->existing_signature_url = isset($config['signature_path']) ? Storage::disk('public')->url($config['signature_path']) : null;
    }

    public function save()
    {
        $config = $this->event->certificate_config ?? [];
        
        if ($this->bg_image) {
            $bgPath = $this->bg_image->store('certificates/backgrounds', 'public');
            $config['bg_path'] = $bgPath;
        }
        
        if ($this->signature_image) {
            $sigPath = $this->signature_image->store('certificates/signatures', 'public');
            $config['signature_path'] = $sigPath;
        }
        
        $config['is_active'] = $this->is_active;
        $config['title'] = $this->title;
        $config['body'] = $this->body;
        $config['signer_name'] = $this->signer_name;
        $config['signer_title'] = $this->signer_title;
        
        $this->event->update([
            'certificate_config' => $config
        ]);

        session()->flash('message', 'Certificate blueprint updated successfully.');
        $this->redirect(route('admin.events.registrants', $this->event), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.event.certificate-config')
            ->layout('layouts.app');
    }
}

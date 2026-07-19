<?php

namespace App\Livewire\Admin\Inquiry;

use App\Models\InquiryForm;
use Livewire\Component;
use Illuminate\Support\Str;

class Index extends Component
{
    public $forms;
    
    public $showModal = false;
    public $editingId = null; // Tambahkan ini
    public $name, $slug;

    public function mount()
    {
        $this->refreshForms();
    }

    public function refreshForms()
    {
        $this->forms = InquiryForm::withCount('submissions')->get();
    }

    public function create()
    {
        $this->reset(['name', 'slug', 'editingId']); // Tambahkan editingId di sini
        $this->showModal = true;
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:inquiry_forms,slug,' . $this->editingId,
        ]);

        if ($this->editingId) {
            $form = InquiryForm::find($this->editingId);
            $form->update([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
        } else {
            InquiryForm::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'fields' => [], 
                'has_categories' => false,
                'notification_emails' => [],
            ]);
        }

        $this->showModal = false;
        $this->refreshForms();
        
        $this->dispatch('swal:success', [
            'title' => 'Success!',
            'text' => $this->editingId ? 'Inquiry type updated.' : 'Inquiry type created.'
        ]);
    }

    public function edit($id)
    {
        $form = InquiryForm::findOrFail($id);
        $this->editingId = $id;
        $this->name = $form->name;
        $this->slug = $form->slug;
        $this->showModal = true;
    }

    public function delete($id)
    {
        InquiryForm::find($id)->delete();
        $this->refreshForms();
    }

    public function render()
    {
        return view('livewire.admin.inquiry.index')
            ->layout('layouts.app');
    }
}

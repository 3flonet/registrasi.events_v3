<?php

namespace App\Livewire\Admin\Inquiry;

use App\Models\InquiryForm;
use App\Models\InquiryCategory;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class Builder extends Component
{
    use WithFileUploads;

    public InquiryForm $form;
    
    // Tab State
    public $activeTab = 'general'; // general, categories, fields, notifications

    // General Tab
    public $name, $slug;
    public $description_en, $description_id;
    public $has_categories = false;
    public $selected_agenda_id;
    public $proposalFile; // Temporary upload
    public $proposalUrl;
    public $thumbnailFile; // Temporary upload (new)
    public $thumbnailUrl; // (new)

    // Categories Tab
    public $categories = []; // Eloquent Collection
    public $showCategoryModal = false;
    public $editingCategory = null; // ID category yg sedang diedit
    public $cat_name_en, $cat_name_id;
    public $cat_desc_en, $cat_desc_id;
    public $cat_price;

    // Fields Tab
    public $fields = []; // Array of field definitions
    public $newFieldType = 'text';
    public $newFieldName = '';
    public $newFieldLabel = '';

    // Notifications Tab
    public $notification_emails = []; // Array
    public $newEmail = '';

    public function mount(InquiryForm $form)
    {
        $this->form = $form;
        
        // Init General
        $this->name = $form->name;
        $this->slug = $form->slug;
        $this->description_en = $form->getTranslation('description', 'en');
        $this->description_id = $form->getTranslation('description', 'id');
        $this->has_categories = $form->has_categories;
        $this->selected_agenda_id = $form->event_agenda_id;
        $this->proposalUrl = $form->getFirstMediaUrl('proposal');
        $this->thumbnailUrl = $form->getFirstMediaUrl('thumbnail'); // Load thumbnail
        
        // Init Fields
        $this->fields = $form->fields ?? [];
        
        // Init Notifications
        $this->notification_emails = $form->notification_emails ?? [];
        
        // Init Categories
        $this->refreshCategories();
    }

    public function refreshCategories()
    {
        $this->categories = $this->form->categories()->get();
    }

    // --- GENERAL TAB ACTIONS ---
    public function saveGeneral()
    {
        $this->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:inquiry_forms,slug,' . $this->form->id,
        ]);

        $this->form->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => ['en' => $this->description_en, 'id' => $this->description_id],
            'has_categories' => $this->has_categories,
            'event_agenda_id' => $this->selected_agenda_id ? (int)$this->selected_agenda_id : null,
        ]);

        // Save Proposal
        if ($this->proposalFile) {
            $this->form->clearMediaCollection('proposal');
            $this->form->addMedia($this->proposalFile->getRealPath())
                ->usingName($this->proposalFile->getClientOriginalName())
                ->toMediaCollection('proposal');
            
            $this->proposalUrl = $this->form->getFirstMediaUrl('proposal');
            $this->proposalFile = null;
        }

        // Save Thumbnail
        if ($this->thumbnailFile) {
            $this->form->clearMediaCollection('thumbnail');
            $this->form->addMedia($this->thumbnailFile->getRealPath())
                ->usingName('thumbnail')
                ->toMediaCollection('thumbnail');
            
            $this->thumbnailUrl = $this->form->getFirstMediaUrl('thumbnail');
            $this->thumbnailFile = null;
        }

        session()->flash('message', 'Pengaturan umum berhasil disimpan.');
    }

    // --- CATEGORIES TAB ACTIONS ---
    public function openCategoryModal($id = null)
    {
        $this->resetCategoryForm();
        if ($id) {
            $cat = InquiryCategory::find($id);
            $this->editingCategory = $id;
            $this->cat_name_en = $cat->getTranslation('name', 'en');
            $this->cat_name_id = $cat->getTranslation('name', 'id');
            $this->cat_desc_en = $cat->getTranslation('description', 'en');
            $this->cat_desc_id = $cat->getTranslation('description', 'id');
            $this->cat_price = $cat->price;
        }
        $this->showCategoryModal = true;
    }

    public function resetCategoryForm()
    {
        $this->editingCategory = null;
        $this->cat_name_en = ''; $this->cat_name_id = '';
        $this->cat_desc_en = ''; $this->cat_desc_id = '';
        $this->cat_price = '';
    }

    public function saveCategory()
    {
        $this->validate([
            'cat_name_en' => 'required',
            'cat_name_id' => 'required',
        ]);

        $data = [
            'name' => ['en' => $this->cat_name_en, 'id' => $this->cat_name_id],
            'description' => ['en' => $this->cat_desc_en, 'id' => $this->cat_desc_id],
            'price' => $this->cat_price ?: null,
            'inquiry_form_id' => $this->form->id,
        ];

        if ($this->editingCategory) {
            InquiryCategory::find($this->editingCategory)->update($data);
        } else {
            InquiryCategory::create($data);
        }

        $this->showCategoryModal = false;
        $this->refreshCategories();
    }

    public function deleteCategory($id)
    {
        InquiryCategory::find($id)->delete();
        $this->refreshCategories();
    }

    // --- FIELDS TAB ACTIONS ---
    public function addField()
    {
        $this->validate([
            'newFieldName' => 'required|alpha_dash',
            'newFieldLabel' => 'required',
        ]);

        $this->fields[] = [
            'name' => $this->newFieldName,
            'label' => $this->newFieldLabel,
            'type' => $this->newFieldType,
            'required' => true,
        ];

        $this->saveFields();
        $this->newFieldName = ''; $this->newFieldLabel = '';
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields); // reindex
        $this->saveFields();
    }

    public function moveField($index, $direction)
    {
        if ($direction === 'up' && $index > 0) {
            $temp = $this->fields[$index];
            $this->fields[$index] = $this->fields[$index - 1];
            $this->fields[$index - 1] = $temp;
        } elseif ($direction === 'down' && $index < count($this->fields) - 1) {
            $temp = $this->fields[$index];
            $this->fields[$index] = $this->fields[$index + 1];
            $this->fields[$index + 1] = $temp;
        }
        $this->saveFields();
    }

    public function saveFields()
    {
        $this->form->update(['fields' => $this->fields]);
        session()->flash('message', 'Fields updated.');
    }

    // --- NOTIFICATIONS TAB ACTIONS ---
    public function addEmail()
    {
        $this->validate(['newEmail' => 'required|email']);
        
        if (!in_array($this->newEmail, $this->notification_emails)) {
            $this->notification_emails[] = $this->newEmail;
            $this->saveNotifications();
        }
        $this->newEmail = '';
    }

    public function removeEmail($index)
    {
        unset($this->notification_emails[$index]);
        $this->notification_emails = array_values($this->notification_emails);
        $this->saveNotifications();
    }

    public function saveNotifications()
    {
        $this->form->update(['notification_emails' => $this->notification_emails]);
        session()->flash('message', 'Notification settings updated.');
    }

    public function render()
    {
        return view('livewire.admin.inquiry.builder', [
            'events' => \App\Models\EventAgenda::orderBy('created_at', 'desc')->get()
        ])->layout('layouts.app');
    }
}

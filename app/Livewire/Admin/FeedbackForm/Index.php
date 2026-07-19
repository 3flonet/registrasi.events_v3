<?php

namespace App\Livewire\Admin\FeedbackForm;

use App\Models\FeedbackForm;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;

    // Properti untuk UI
    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;
    public $search = '';
    public $selectedIdForDeletion;

    // Properti untuk form
    public $formId;
    public $name;
    public $section_title;
    public $section_description;
    public array $fields = [];
    public array $autoSyncKeys = []; // Melacak field mana yang masih tersinkron otomatis

    protected $rules = [
        'name' => 'required|string|max:255',
        'fields.*.label' => 'required|string',
        'fields.*.name' => 'required_unless:fields.*.type,section|nullable|string',
        'fields.*.type' => 'required|in:text,textarea,select,rating,radio,section',
        'fields.*.required' => 'required|boolean',
        'fields.*.options' => 'nullable|string',
        'fields.*.description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function addField($type = 'text')
    {
        $index = count($this->fields);
        $this->fields[] = [
            'id' => 'field_' . uniqid(),
            'label' => '', 
            'name' => ($type === 'section' ? '' : ''), 
            'type' => $type, 
            'required' => false, 
            'options' => '',
            'description' => ''
        ];
        $this->autoSyncKeys[$index] = ($type !== 'section');
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
        
        unset($this->autoSyncKeys[$index]);
        $this->autoSyncKeys = array_values($this->autoSyncKeys);
    }

    public function reorderFields($orderedIndices)
    {
        $newFields = [];
        $newAutoSync = [];
        
        foreach ($orderedIndices as $newPos => $oldIndex) {
            if (isset($this->fields[$oldIndex])) {
                $newFields[] = $this->fields[$oldIndex];
                $newAutoSync[] = $this->autoSyncKeys[$oldIndex] ?? false;
            }
        }
        
        $this->fields = $newFields;
        $this->autoSyncKeys = $newAutoSync;
    }

    public function updated($property, $value)
    {
        // 1. Validasi Real-time
        $this->validateOnly($property);

        // 2. Logika Auto-Sync Label ke Name
        if (str_starts_with($property, 'fields.') && str_ends_with($property, '.label')) {
            $parts = explode('.', $property);
            $index = $parts[1];

            // Jangan sinkron jika tipe adalah section
            if ($this->fields[$index]['type'] === 'section') {
                return;
            }

            if (isset($this->autoSyncKeys[$index]) && $this->autoSyncKeys[$index]) {
                $this->fields[$index]['name'] = str_replace('-', '_', Str::slug($value));
                $this->validateOnly("fields.$index.name");
            }
        }

        // 3. Matikan Auto-Sync jika user mengedit Name secara manual
        if (str_starts_with($property, 'fields.') && str_ends_with($property, '.name')) {
            $parts = explode('.', $property);
            $index = $parts[1];
            $this->autoSyncKeys[$index] = false;
        }
    }

    protected $messages = [
        'name.required' => 'Instrument identity is required.',
        'fields.*.label.required' => 'Question title is mandatory.',
        'fields.*.name.required' => 'System node key is required for data mapping.',
        'fields.*.type.required' => 'Please select an ingestion interface type.',
    ];

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    public function edit($id)
    {
        $form = FeedbackForm::findOrFail($id);
        $this->formId = $form->id;
        $this->name = $form->name;
        $this->section_title = $form->section_title;
        $this->section_description = $form->section_description;
        $this->fields = $form->fields ?? [];
        // Pastikan setiap field punya ID untuk keperluan sortable/Livewire tracking
        foreach($this->fields as $index => &$field) {
            if (!isset($field['id'])) {
                $field['id'] = 'field_' . uniqid() . '_' . $index;
            }
        }
        
        // Saat edit, kita asumsikan semua field sudah manual kecuali yang kosong
        $this->autoSyncKeys = [];
        foreach($this->fields as $index => $field) {
            $this->autoSyncKeys[$index] = empty($field['name']);
        }
        $this->isEditMode = true;
        $this->showModal = true;
        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'section_title' => $this->section_title,
            'section_description' => $this->section_description,
            'fields' => $this->fields,
            'organizer_id' => auth()->user()->organizer_id,
        ];

        if ($this->isEditMode) {
            FeedbackForm::findOrFail($this->formId)->update($data);
        } else {
            FeedbackForm::create($data);
        }

        $this->closeModal();
        session()->flash('message', 'Feedback form saved successfully.');
    }

    public function confirmDelete($id)
    {
        $this->selectedIdForDeletion = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->selectedIdForDeletion) {
            FeedbackForm::findOrFail($this->selectedIdForDeletion)->delete();
            $this->showDeleteModal = false;
            $this->selectedIdForDeletion = null;
            session()->flash('message', 'Feedback form deleted successfully.');
        }
    }

    private function resetForm()
    {
        $this->formId = null;
        $this->name = '';
        $this->section_title = '';
        $this->section_description = '';
        $this->fields = [];
        $this->autoSyncKeys = [];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $forms = FeedbackForm::where('organizer_id', auth()->user()->organizer_id)
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.feedback-form.index', ['forms' => $forms])
            ->layout('layouts.app');
    }
}

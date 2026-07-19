<?php

namespace App\Livewire\Admin\MessageTemplate;

use App\Models\MessageTemplateCategory;
use Livewire\Component;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    public $categories;
    public $showForm = false;
    
    // Form fields
    public $categoryId;
    public $name;
    public $slug;
    public $icon = 'fa-folder';
    public $color = 'slate';
    public $description;
    public $is_manual_sendable = true;
    public $is_system = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'icon' => 'required|string',
        'color' => 'required|string',
        'description' => 'nullable|string|max:255',
        'is_manual_sendable' => 'boolean',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = MessageTemplateCategory::orderByRaw("CASE WHEN slug = 'others' THEN 1 ELSE 0 END ASC")
            ->orderBy('id', 'ASC')
            ->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $category = MessageTemplateCategory::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->icon = $category->icon;
        $this->color = $category->color;
        $this->description = $category->description;
        $this->is_manual_sendable = $category->is_manual_sendable;
        $this->is_system = $category->is_system;
        
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->is_system ? $this->slug : Str::slug($this->slug),
            'icon' => $this->icon,
            'color' => $this->color,
            'description' => $this->description,
            'is_manual_sendable' => $this->is_manual_sendable,
        ];

        MessageTemplateCategory::updateOrCreate(['id' => $this->categoryId], $data);

        session()->flash('message', 'Category saved successfully.');
        $this->showForm = false;
        $this->loadCategories();
    }

    public function delete($id)
    {
        $category = MessageTemplateCategory::findOrFail($id);
        if ($category->is_system) {
            session()->flash('error', 'System categories cannot be deleted.');
            return;
        }
        
        $category->delete();
        session()->flash('message', 'Category deleted.');
        $this->loadCategories();
    }

    public function updatedName($value)
    {
        if (!$this->categoryId && !$this->is_system) {
            $this->slug = Str::slug($value);
        }
    }

    public function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->slug = '';
        $this->icon = 'fa-folder';
        $this->color = 'slate';
        $this->description = '';
        $this->is_manual_sendable = true;
        $this->is_system = false;
    }

    public function render()
    {
        return view('livewire.admin.message-template.category-manager')
            ->layout('layouts.app');
    }
}

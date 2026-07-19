<?php

namespace App\Livewire\Admin\SectionTemplate;

use App\Models\SectionTemplate;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Form extends Component
{
    use \Livewire\WithFileUploads;

    public $templateId;
    public $name;
    public $slug;
    public $html_content;
    public $css_content;
    public $fields = [];
    public $thumbnail;
    public $existingThumbnail;
    public bool $isEditMode = false;

    public function mount($template = null)
    {
        if ($template) {
            $template = SectionTemplate::findOrFail($template);
            $this->templateId = $template->id;
            $this->name = $template->name;
            $this->slug = $template->slug;
            $this->html_content = $template->html_content;
            $this->css_content = $template->css_content;
            $this->fields = $template->fields ?? [];
            $this->existingThumbnail = $template->thumbnail;
            $this->isEditMode = true;
        } else {
            $this->fields = [];
        }
    }

    public function updatedName($value)
    {
        if (!$this->isEditMode) {
            $this->slug = Str::slug($value);
        }
    }

    public function addField()
    {
        $this->fields[] = ['name' => '', 'type' => 'text', 'label' => ''];
    }

    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                Rule::unique('section_templates')->ignore($this->templateId),
            ],
            'html_content' => 'required|string',
            'css_content' => 'nullable|string',
            'fields' => 'present|array',
            'fields.*.label' => 'required|string',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $thumbnailPath = $this->existingThumbnail;
        if ($this->thumbnail) {
            $thumbnailPath = $this->thumbnail->store('templates', 'public');
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'html_content' => $this->html_content,
            'css_content' => $this->css_content,
            'fields' => $this->fields,
            'thumbnail' => $thumbnailPath,
        ];

        if ($this->isEditMode) {
            $template = SectionTemplate::findOrFail($this->templateId);
            $template->update($data);
            session()->flash('message', 'Architecture blueprint updated.');
        } else {
            SectionTemplate::create($data);
            session()->flash('message', 'New architecture blueprint forged.');
        }

        return $this->redirect(route('admin.section-templates.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.section-template.form')
            ->layout('layouts.app');
    }
}

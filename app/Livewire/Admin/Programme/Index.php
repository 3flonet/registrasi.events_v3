<?php

namespace App\Livewire\Admin\Programme;
 
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\EventProgramme;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $isOpen = false;
    public $programmeId;

    // Removed $event_ids
    
    public $title = []; // Array for translations
    public $description = []; // Array for translations
    public $start_time;
    public $end_time;
    public $link_url;
    public $banner; // New
    public $existingBanner; // New

    protected $rules = [
        'title.id' => 'required|string|max:255',
        'title.en' => 'nullable|string|max:255',
        'start_time' => 'nullable|date',
        'end_time' => 'nullable|date|after_or_equal:start_time',
        'link_url' => 'nullable|url',
        'banner' => 'nullable|image|max:2048',
    ];

    public function render()
    {
        return view('livewire.admin.programme.index', [
            'programmes' => EventProgramme::latest('start_time')->paginate(10),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->title = ['id' => '', 'en' => ''];
        $this->description = ['id' => '', 'en' => ''];
        $this->start_time = null;
        $this->end_time = null;
        $this->link_url = '';
        $this->banner = null;
        $this->existingBanner = null;
        $this->programmeId = null;
    }

    public function store()
    {
        $this->validate();

        // Handle File Upload
        $bannerPath = $this->programmeId ? EventProgramme::find($this->programmeId)->banner_path : null;
        if ($this->banner) {
            $bannerPath = $this->banner->store('programmes', 'public');
        }

        EventProgramme::updateOrCreate(['id' => $this->programmeId], [
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'link_url' => $this->link_url,
            'banner_path' => $bannerPath,
        ]);

        session()->flash('message', $this->programmeId ? 'Program Updated Successfully.' : 'Program Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $programme = EventProgramme::findOrFail($id);
        $this->programmeId = $id;

        $this->title = $programme->getTranslations('title');
        $this->description = $programme->getTranslations('description');
        $this->start_time = $programme->start_time ? $programme->start_time->format('Y-m-d\TH:i') : null;
        $this->end_time = $programme->end_time ? $programme->end_time->format('Y-m-d\TH:i') : null;
        $this->link_url = $programme->link_url;
        $this->existingBanner = $programme->banner_path;

        $this->openModal();
    }

    public function delete($id)
    {
        EventProgramme::find($id)->delete();
        session()->flash('message', 'Program Deleted Successfully.');
    }
}

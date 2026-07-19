<?php

namespace App\Livewire\Admin\Agenda;
 
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\EventAgenda;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $isOpen = false;
    public $agendaId;
    
    public $title = []; // Changed to array
    public $description = []; // Changed to array
    public $start_time;
    public $end_time;
    public $link_url;
    public $banner; // For new upload
    public $existingBanner; // For viewing existing image during edit

    protected $rules = [
        'title.id' => 'required|string|max:255',
        'title.en' => 'nullable|string|max:255',
        'start_time' => 'nullable|date',
        'end_time' => 'nullable|date|after_or_equal:start_time',
        'link_url' => 'nullable|url',
        'banner' => 'nullable|image|max:2048', // 2MB Max
    ];

    public function render()
    {
        return view('livewire.admin.agenda.index', [
            'agendas' => EventAgenda::latest('start_time')->paginate(10),
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
        $this->agendaId = null;
    }

    public function store()
    {
        $this->validate();

        // Handle File Upload
        $bannerPath = $this->agendaId ? EventAgenda::find($this->agendaId)->banner_path : null;
        if ($this->banner) {
            $bannerPath = $this->banner->store('agendas', 'public');
        }

        EventAgenda::updateOrCreate(['id' => $this->agendaId], [
            'title' => $this->title,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'link_url' => $this->link_url,
            'banner_path' => $bannerPath,
        ]);

        session()->flash('message', $this->agendaId ? 'Agenda Updated Successfully.' : 'Agenda Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $agenda = EventAgenda::findOrFail($id);
        $this->agendaId = $id;
        
        $this->title = $agenda->getTranslations('title');
        $this->description = $agenda->getTranslations('description');
        $this->start_time = $agenda->start_time ? $agenda->start_time->format('Y-m-d\TH:i') : null;
        $this->end_time = $agenda->end_time ? $agenda->end_time->format('Y-m-d\TH:i') : null;
        $this->link_url = $agenda->link_url;
        $this->existingBanner = $agenda->banner_path;

        $this->openModal();
    }

    public function delete($id)
    {
        EventAgenda::find($id)->delete();
        session()->flash('message', 'Agenda Deleted Successfully.');
    }
}
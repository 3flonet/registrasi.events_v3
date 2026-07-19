<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $events = Event::query()
            ->where('is_active', true)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('livewire.admin.checkin.index', [
            'events' => $events
        ])->layout('layouts.app');
    }
}

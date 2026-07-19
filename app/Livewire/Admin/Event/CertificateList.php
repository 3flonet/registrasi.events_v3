<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class CertificateList extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $events = Event::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.event.certificate-list', [
            'events' => $events
        ])->layout('layouts.app');
    }
}

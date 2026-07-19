<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\EventProgramme as ProgrammeModel;
use Carbon\Carbon;

class EventProgramme extends Component
{
    public $search = '';

    public function render()
    {
        $query = ProgrammeModel::query();

        // 1. Filter Search (JSON support)
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title->id', 'like', '%' . $this->search . '%')
                  ->orWhere('title->en', 'like', '%' . $this->search . '%');
            });
        }
        
        $programmes = $query->orderBy('start_time', 'asc')
            ->get()
            ->groupBy(function ($date) {
                if (!$date->start_time) return 'TBA';
                return Carbon::parse($date->start_time)->format('Y-m-d');
            });

        return view('livewire.public.event-programme', [
            'groupedProgrammes' => $programmes
        ])->layout('layouts.guest');
    }
}

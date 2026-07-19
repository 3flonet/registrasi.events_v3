<?php

namespace App\Livewire\Admin\SectionTemplate;

use App\Models\SectionTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $templates = SectionTemplate::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.admin.section-template.index', [
            'templates' => $templates,
        ])->layout('layouts.app');
    }

    public function delete($id)
    {
        SectionTemplate::findOrFail($id)->delete();
        session()->flash('message', 'Architecture blueprint purged from archive.');
    }
}

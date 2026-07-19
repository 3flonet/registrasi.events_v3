<?php

namespace App\Livewire\Public;

use App\Models\InquiryForm;
use Livewire\Component;
use Livewire\WithPagination;

class SubmissionsResult extends Component
{
    use WithPagination;

    public InquiryForm $form;

    public function mount(InquiryForm $form)
    {
        $this->form = $form;
    }

    public function render()
    {
        // Pastikan hanya admin/staff yang bisa melihat ini
        if (!auth()->check()) {
            abort(403);
        }

        $submissions = $this->form->submissions()->latest()->paginate(15);

        return view('livewire.public.submissions-result', [
            'submissions' => $submissions
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\Admin\Inquiry;

use App\Models\InquirySubmission;
use App\Models\InquiryForm;
use Livewire\Component;
use Livewire\WithPagination;

class Monitoring extends Component
{
    use WithPagination;

    public $filterFormId = '';
    public $filterStatus = '';
    public $search = '';

    // Modal Detail
    public $showDetailModal = false;
    public $selectedSubmission = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openDetail($id)
    {
        $this->selectedSubmission = InquirySubmission::with(['form', 'category', 'agenda'])->find($id);
        $this->showDetailModal = true;
    }

    public function updateStatus($status)
    {
        if ($this->selectedSubmission) {
            $this->selectedSubmission->update(['status' => $status]);
            $this->selectedSubmission->refresh();
            session()->flash('message', 'Status updated to ' . ucfirst($status));
        }
    }

    public function render()
    {
        $query = InquirySubmission::with(['form', 'category', 'agenda'])
            ->latest();

        if ($this->filterFormId) {
            $query->where('inquiry_form_id', $this->filterFormId);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        
        // Search logic (agak kompleks karena data ada di JSON fields)
        if ($this->search) {
             $query->where(function($q) {
                 // Cari di nama form, atau status
                 $q->whereHas('form', function($f) {
                     $f->where('name', 'like', '%' . $this->search . '%');
                 })
                 ->orWhere('status', 'like', '%' . $this->search . '%')
                 // Atau coba cari kasar di kolom JSON data
                 ->orWhere('data', 'like', '%' . $this->search . '%');
             });
        }

        $submissions = $query->paginate(15);

        return view('livewire.admin.inquiry.monitoring', [
            'submissions' => $submissions,
            'forms' => InquiryForm::all(),
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\Public\Inquiry;

use App\Models\InquirySubmission;
use Livewire\Component;

class Success extends Component
{
    public InquirySubmission $submission;
    public $proposalUrl;

    public function mount(InquirySubmission $submission)
    {
        $this->submission = $submission;
        $this->submission->load('form');
        
        // Cek apakah Form memiliki proposal yang diupload
        $this->proposalUrl = $this->submission->form->getFirstMediaUrl('proposal');
    }

    public function render()
    {
        return view('livewire.public.inquiry.success')->layout('layouts.guest');
    }
}

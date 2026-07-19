<?php

namespace App\Livewire\Public\Inquiry;

use App\Models\InquiryForm;
use App\Models\InquiryCategory;
use App\Models\InquirySubmission;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
use App\Mail\InquirySubmitted; // Kita akan buat nanti
use App\Mail\InquiryReceived; // Kita akan buat nanti

class Form extends Component
{
    use WithFileUploads;

    public InquiryForm $form;
    
    // State
    public $step = 1; // 1: Intro, 2: Agenda, 3: Category, 4: Form
    public $selected_agenda_id;
    public $selected_category_id;
    
    // Data Pembantu UI
    public $agendas = [];
    public $categories = [];
    public $selectedCategoryModel = null;

    // Dynamic Form Data
    public $formData = []; 
    public $tempFiles = []; 

    public function mount(InquiryForm $form)
    {
        $this->form = $form;
        
        // 1. FILTER AGENDA
        if ($this->form->event_agenda_id) {
            $agenda = \App\Models\EventAgenda::find($this->form->event_agenda_id);
            if ($agenda) {
                $this->agendas = collect([$agenda]);
            } else {
                $this->agendas = collect([]); 
            }
        } else {
            $this->agendas = \App\Models\EventAgenda::where('start_time', '>=', now())
                ->orderBy('start_time')
                ->get();
        }

        // 2. AUTO SELECT
        if ($this->agendas->count() === 1) {
            $this->selected_agenda_id = $this->agendas->first()->id;
        }

        // 3. Load Categories
        if ($this->form->has_categories) {
            $this->categories = $this->form->categories()->where('is_active', true)->get();
        }

        // 4. Default Step is 1 (Intro)
        $this->step = 1;
        
        // 5. Init dynamic form fields
        if (!empty($this->form->fields)) {
            foreach ($this->form->fields as $field) {
                $this->formData[$field['name']] = '';
            }
        }
    }

    public function startInquiry()
    {
        // Calculate next step from Intro
        if (!$this->selected_agenda_id && $this->agendas->count() > 0) {
            $this->step = 2; // Go to Agenda
        } elseif ($this->form->has_categories && !$this->selected_category_id) {
            $this->step = 3; // Go to Category
        } else {
            $this->step = 4; // Go to Form
        }
    }

    public function selectAgenda($id)
    {
        $this->selected_agenda_id = $id;
        
        // Next Step
        if ($this->form->has_categories && !$this->selected_category_id) {
            $this->step = 3;
        } else {
            $this->step = 4;
        }
    }

    public function selectCategory($id)
    {
        $this->selected_category_id = $id;
        $this->selectedCategoryModel = InquiryCategory::find($id);
        $this->step = 4;
    }

    public function backStep()
    {
        if ($this->step == 4) {
            // From Form
            if ($this->form->has_categories) {
                $this->step = 3; // Back to Category
                $this->selected_category_id = null;
            } elseif (!$this->form->event_agenda_id && $this->agendas->count() > 1) {
                 // Check agendas count > 1 because if only 1, it was auto-selected, so skipping Agenda step
                $this->step = 2; // Back to Agenda
                $this->selected_agenda_id = null;
            } else {
                $this->step = 1; // Back to Intro
            }
        } elseif ($this->step == 3) {
            // From Category
            if (!$this->form->event_agenda_id && $this->agendas->count() > 1) {
                $this->step = 2; // Back to Agenda
                $this->selected_agenda_id = null;
            } else {
                $this->step = 1; // Back to Intro
            }
        } elseif ($this->step == 2) {
             // From Agenda
            $this->step = 1; // Back to Intro
            $this->selected_agenda_id = null;
        }
    }

    public function submit()
    {
        // 1. Build Validation Rules
        $rules = [];
        $messages = [];
        if (!empty($this->form->fields)) {
            foreach ($this->form->fields as $field) {
                if (isset($field['required']) && $field['required']) {
                    $key = 'formData.' . $field['name'];
                    if ($field['type'] === 'file') {
                        $rules['tempFiles.' . $field['name']] = 'required|file|max:5120'; // 5MB
                    } else {
                        $rules[$key] = 'required';
                        if ($field['type'] === 'email') $rules[$key] .= '|email';
                    }
                    $messages[$key . '.required'] = $field['label'] . ' wajib diisi.';
                }
            }
        }
        
        $this->validate($rules, $messages);

        // 2. Process Files & Data
        $finalData = $this->formData;
        
        // Handle File Uploads (pindahkan dari tempFiles ke formData[key] sebagai path/url nanti)
        // Tapi InquirySubmission punya fitur MediaLibrary, jadi kita simpan file fisik di Submission model
        // Kita kosongkan value file di $finalData agart tidak menyimpan object livewire temporary
        
        // 3. Create Submission
        $submission = InquirySubmission::create([
            'inquiry_form_id' => $this->form->id,
            'inquiry_category_id' => $this->selected_category_id,
            'event_agenda_id' => $this->selected_agenda_id,
            'data' => $finalData,
            'status' => 'pending',
        ]);

        // 4. Attach Files
        if (!empty($this->form->fields)) {
            foreach ($this->form->fields as $field) {
                if ($field['type'] === 'file' && isset($this->tempFiles[$field['name']])) {
                    $file = $this->tempFiles[$field['name']];
                    $submission->addMedia($file->getRealPath())
                        ->usingName($file->getClientOriginalName())
                        ->toMediaCollection('attachments');
                        
                    // Update data JSON to include file URL (optional, easier access)
                    // $submission->update(['data' => array_merge($submission->data, [$field['name'] => 'See Media Collection'])]);
                }
            }
        }

// 5. Send Emails
        try {
            // Email to User (jika field 'email' ada di form data)
            if (isset($finalData['email']) && filter_var($finalData['email'], FILTER_VALIDATE_EMAIL)) {
                Mail::to($finalData['email'])->send(new \App\Mail\InquirySubmitted($submission, $this->form->getFirstMediaUrl('proposal')));
            }

            // Email to Admin (Notification)
            if (!empty($this->form->notification_emails)) {
                Mail::to($this->form->notification_emails)->send(new \App\Mail\InquiryReceived($submission));
            }
        } catch (\Exception $e) {
            // Log error email but don't fail the submission
            \Illuminate\Support\Facades\Log::error('Inquiry Email Error: ' . $e->getMessage());
        }

        // 6. Redirect to Success
        return redirect()->route('public.inquiry.success', $submission->id);
    }

    public function render()
    {
        return view('livewire.public.inquiry.form', [
             'agendaModel' => $this->selected_agenda_id ? \App\Models\EventAgenda::find($this->selected_agenda_id) : null
        ])->layout('layouts.guest');
    }
}

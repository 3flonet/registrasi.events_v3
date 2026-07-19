<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use App\Models\InquiryForm;
use App\Models\EventEmailTemplate;
use App\Models\FeedbackForm;
use App\Models\EventSessionGroup;
use App\Models\EventSession;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class Form extends Component
{
    use WithFileUploads;

    public $event_id;
    public $organizer_id; // For Super Admin selection
    public $isEditMode = false;
    public $currentStep = 1;
    public $maxSteps = 9;

    // Core Properties
    public $name_en, $name_id, $slug;
    public $description_en, $description_id;
    public $quota = 0;
    public $is_active = '1';
    public $slugAvailable = null;
    public $status = 'upcoming';
    public $visibility = 'public';
    public $requires_account = false;
    public $is_paid_event = false;
    public $fee_payer = 'organizer';
    public $payment_expiry_duration = 1440;

    // Media
    public $banner;
    public $existingBannerUrl;
    public $drive_banner_path = null;
    public $og_image;
    public $existingOgImageUrl;
    public $drive_og_image_path = null;
    public $speaker_uploads = [];
    public $moderator_uploads = [];
    public $sponsor_uploads = [];
    public $showFilePicker = false;
    public $activePickerTarget = null;

    // Venue & Online info
    public $type = 'offline'; // offline, online, hybrid
    public $venue_en, $venue_id;
    public $google_maps_iframe = '';
    public $platform;
    public $meeting_link;
    public $meeting_info = [];

    // Content & Personnel
    public array $daily_schedules = [];
    public array $personnel = ['speakers' => [], 'moderators' => []];
    public array $sponsors = [];
    public array $youtube_recordings = [];
    public $theme_en, $theme_id;

    // Integrations
    public $inquiry_form_id = null;
    public $confirmation_template_id = null;
    public $checkin_template_id = null;
    public $invoice_template_id = null;
    public $reminder_template_id = null;
    public $certificate_template_id = null;
    public $feedback_template_id = null;
    public $confirmationTemplates = [];
    public $checkinTemplates = [];
    public $invoiceTemplates = [];
    public $reminderTemplates = [];
    public $certificateTemplates = [];
    public $feedbackTemplates = [];
    public $inquiryForms;
    public $allFeedbackForms = [];
    public $organizers = []; // For Super Admin
    public array $ticket_tiers = [];
    public $feedback_form_id = null;
    public $is_feedback_active = false;
    public $external_registration_link;
    public $use_external_link = false;
    public $partnership_link;
    public $use_partnership_link = false;

    public $registrant_limit = -1;

    public array $field_config = [];
    public array $session_groups = [];

    public function addSessionGroup()
    {
        $this->session_groups[] = [
            'id' => null,
            'name' => '',
            'selection_type' => 'single',
            'is_required' => false,
            'sessions' => [
                [
                    'id' => null,
                    'event_agenda_id' => null,
                    'title' => ['en' => '', 'id' => ''],
                    'description' => ['en' => '', 'id' => ''],
                    'room_name' => '',
                    'start_time' => '',
                    'end_time' => '',
                    'quota' => -1,
                    'is_checkin_active' => false,
                ]
            ],
        ];
    }

    public function removeSessionGroup($groupIdx)
    {
        unset($this->session_groups[$groupIdx]);
        $this->session_groups = array_values($this->session_groups);
    }

    public function addSession($groupIdx)
    {
        $this->session_groups[$groupIdx]['sessions'][] = [
            'id' => null,
            'event_agenda_id' => null,
            'title' => ['en' => '', 'id' => ''],
            'description' => ['en' => '', 'id' => ''],
            'room_name' => '',
            'start_time' => '',
            'end_time' => '',
            'quota' => -1,
            'is_checkin_active' => false,
        ];
    }

    public function removeSession($groupIdx, $sessionIdx)
    {
        unset($this->session_groups[$groupIdx]['sessions'][$sessionIdx]);
        $this->session_groups[$groupIdx]['sessions'] = array_values($this->session_groups[$groupIdx]['sessions']);
    }

    public function mount($event = null)
    {
        $user = auth()->user();
        $organizer = $user->organizer;
        $plan = $organizer ? $organizer->subscriptionPlan : null;
        $this->registrant_limit = $plan ? $plan->registrant_limit : -1;

        // If Super Admin, fetch all organizers
        if ($user->hasRole('Super Admin')) {
            $this->organizers = \App\Models\Organizer::orderBy('name')->get();
        }

        // Quota Guard for New Events
        if (!$event && $plan && $plan->event_limit != -1) {
            $used = \App\Models\Event::count();
            if ($used >= $plan->event_limit) {
                session()->flash('error', 'You have reached the limit of ' . $plan->event_limit . ' events allowed by your current plan. Please upgrade to create more.');
                return $this->redirect(route('admin.events.index'), navigate: true);
            }
        }

        if (auth()->user()->hasRole('Super Admin')) {
            $this->inquiryForms = InquiryForm::orderBy('name')->get();
            $this->allFeedbackForms = FeedbackForm::orderBy('name')->get();
        } else {
            $this->inquiryForms = InquiryForm::where('organizer_id', auth()->user()->organizer_id)->get();
            $this->allFeedbackForms = FeedbackForm::where('organizer_id', auth()->user()->organizer_id)->get();
        }
        $this->loadTemplates();



        if ($event) {
            $eventModel = $event instanceof Event ? $event : Event::findOrFail($event);
            $this->event_id = $eventModel->id;
            $this->isEditMode = true;
            $this->loadEventData($eventModel);
        } else {
            $this->isEditMode = false;
            $this->daily_schedules = [];
            if (empty($this->daily_schedules)) {
                $this->addSchedule();
            }
            $this->field_config = [
                'nama_instansi' => ['active' => false, 'required' => false],
                'tipe_instansi' => ['active' => false, 'required' => false, 'options' => 'Pemerintahan, Swasta, BUMN, Universitas, Sekolah, Lainnya'],
                'jabatan'       => ['active' => false, 'required' => false],
                'alamat'        => ['active' => false, 'required' => false],
                'tanda_tangan'  => ['active' => false, 'required' => false],
            ];
            $this->session_groups = [];
        }
    }

    private function loadEventData($event)
    {
        $this->name_en = $event->getTranslation('name', 'en');
        $this->name_id = $event->getTranslation('name', 'id');
        $this->slug = $event->slug;
        $this->description_en = $event->getTranslation('description', 'en');
        $this->description_id = $event->getTranslation('description', 'id');
        $this->quota = $event->quota;
        $this->is_active = $event->is_active ? '1' : '0';
        $this->status = $event->status;
        $this->visibility = $event->visibility;
        $this->requires_account = (bool)$event->requires_account;
        $this->is_paid_event = (bool)$event->is_paid_event;
        $this->fee_payer = $event->fee_payer ?? 'organizer';
        $this->payment_expiry_duration = $event->payment_expiry_duration ?? 1440;
        $this->type = $event->type;
        $this->venue_en = $event->getTranslation('venue', 'en');
        $this->venue_id = $event->getTranslation('venue', 'id');
        $this->google_maps_iframe = $event->google_maps_iframe;
        $this->platform = $event->platform;
        $this->meeting_link = $event->meeting_link;
        $this->meeting_info = $event->meeting_info ?? [];
        $this->daily_schedules = $event->daily_schedules ?? [];
        $this->personnel = $event->personnel ?? ['speakers' => [], 'moderators' => []];
        $this->sponsors = $event->sponsors ?? [];
        $this->youtube_recordings = $event->youtube_recordings ?? [];
        $this->theme_en = $event->getTranslation('theme', 'en');
        $this->theme_id = $event->getTranslation('theme', 'id');
        $this->inquiry_form_id = $event->inquiry_form_id;
        $this->confirmation_template_id = $event->confirmation_template_id;
        $this->checkin_template_id = $event->checkin_template_id;
        $this->invoice_template_id = $event->invoice_template_id;
        $this->reminder_template_id = $event->reminder_template_id;
        $this->certificate_template_id = $event->certificate_template_id;
        $this->feedback_template_id = $event->feedback_template_id;
        $this->feedback_form_id = $event->feedback_form_id;
        $this->is_feedback_active = (bool)$event->is_feedback_active;
        $this->external_registration_link = $event->external_registration_link;
        $this->use_external_link = !empty($event->external_registration_link);
        $this->partnership_link = $event->partnership_link;
        $this->use_partnership_link = !empty($event->partnership_link);
        $this->organizer_id = $event->organizer_id;
        $this->existingBannerUrl = $event->getFirstMediaUrl('default', 'card-banner');
        $this->existingOgImageUrl = $event->getFirstMediaUrl('og_image');
        $this->field_config = $event->field_config ?? [
            'nama_instansi' => ['active' => false, 'required' => false],
            'tipe_instansi' => ['active' => false, 'required' => false, 'options' => 'Pemerintahan, Swasta, BUMN, Universitas, Sekolah, Lainnya'],
            'jabatan'       => ['active' => false, 'required' => false],
            'alamat'        => ['active' => false, 'required' => false],
            'tanda_tangan'  => ['active' => false, 'required' => false],
        ];

        $this->session_groups = $event->sessionGroups->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'selection_type' => $group->selection_type,
                'is_required' => $group->is_required,
                'sessions' => $group->sessions->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'event_agenda_id' => $session->event_agenda_id,
                        'title' => $session->getTranslations('title'),
                        'description' => $session->getTranslations('description'),
                        'room_name' => $session->room_name,
                        'start_time' => $session->start_time ? $session->start_time->format('Y-m-d\TH:i') : null,
                        'end_time' => $session->end_time ? $session->end_time->format('Y-m-d\TH:i') : null,
                        'quota' => $session->quota,
                        'is_checkin_active' => $session->is_checkin_active,
                    ];
                })->toArray(),
            ];
        })->toArray();

        // Normalize daily schedules to ensure speaker_ids and moderator_ids are always arrays
        if (!empty($this->daily_schedules)) {
            foreach ($this->daily_schedules as &$day) {
                if (isset($day['agenda'])) {
                    foreach ($day['agenda'] as &$session) {
                        if (!isset($session['speaker_ids']) || !is_array($session['speaker_ids'])) {
                            $session['speaker_ids'] = [];
                        }
                        if (!isset($session['moderator_ids']) || !is_array($session['moderator_ids'])) {
                            $session['moderator_ids'] = [];
                        }
                        if (!isset($session['is_active'])) {
                            $session['is_active'] = true;
                        }
                    }
                }
            }
        }

        
        $this->ticket_tiers = $event->ticketTiers->map(fn($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'price' => $t->price,
            'quota' => $t->quota,
            'description' => $t->description,
            'sales_start_at' => $t->sales_start_at ? $t->sales_start_at->format('Y-m-d\TH:i') : null,
            'sales_end_at' => $t->sales_end_at ? $t->sales_end_at->format('Y-m-d\TH:i') : null,
        ])->toArray();

        if (empty($this->ticket_tiers)) {
            $this->addTicketTier();
        }

        $this->confirmationTemplates = EventEmailTemplate::where(function($q) use ($event) {
                $q->where('event_id', $event->id)->orWhereNull('event_id');
            })
            ->where('category', 'transactional')
            ->get();

        $this->checkinTemplates = EventEmailTemplate::where(function($q) use ($event) {
                $q->where('event_id', $event->id)->orWhereNull('event_id');
            })
            ->where('category', 'auto_checkin')
            ->get();

        $this->invoiceTemplates = EventEmailTemplate::where(function($q) use ($event) {
                $q->where('event_id', $event->id)->orWhereNull('event_id');
            })
            ->where('category', 'event_invoice')
            ->get();

        $this->reminderTemplates = EventEmailTemplate::where(function($q) use ($event) {
                $q->where('event_id', $event->id)->orWhereNull('event_id');
            })
            ->where('category', 'reminder')
            ->get();

        $this->certificateTemplates = EventEmailTemplate::where(function($q) use ($event) {
                $q->where('event_id', $event->id)->orWhereNull('event_id');
            })
            ->where('category', 'certificate')
            ->get();

        $this->feedbackTemplates = EventEmailTemplate::where(function($q) use ($event) {
                $q->where('event_id', $event->id)->orWhereNull('event_id');
            })
            ->where('category', 'event_feedback')
            ->get();
    }

    public function updatedNameEn($value)
    {
        $this->slug = Str::slug($value);
        $this->updatedSlug($this->slug);
    }

    public function getEventEndDateProperty()
    {
        $allDatetimes = [];
        foreach ($this->daily_schedules as $schedule) {
            if (!empty($schedule['agenda'])) {
                foreach ($schedule['agenda'] as $agendaItem) {
                    $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['end_time']);
                }
            } else if (!empty($schedule['date'])) {
                $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'])->endOfDay();
            }
        }
        return !empty($allDatetimes) ? max($allDatetimes)->format('Y-m-d\TH:i') : now()->addYears(1)->format('Y-m-d\TH:i');
    }

    // Wizard Navigation
    public function nextStep()
    {
        $this->validateCurrentStep();
        if ($this->currentStep < $this->maxSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function updatedSlug($value)
    {
        if (empty($value)) {
            $this->slugAvailable = null;
            return;
        }

        $exists = \App\Models\Event::where('slug', $value)
            ->where('id', '!=', $this->event_id)
            ->exists();

        $this->slugAvailable = !$exists;
    }

    public function setStep($step)
    {
        // Allow jumping back any time, or jumping ahead in edit mode
        if ($step < $this->currentStep || $this->isEditMode) {
            $this->currentStep = $step;
        }
    }

    public function getComputedStatusProperty()
    {
        $allDatetimes = [];
        foreach ($this->daily_schedules as $schedule) {
            if (!empty($schedule['agenda'])) {
                foreach ($schedule['agenda'] as $agendaItem) {
                    if (!empty($schedule['date']) && !empty($agendaItem['start_time'])) {
                        $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['start_time']);
                    }
                    if (!empty($schedule['date']) && !empty($agendaItem['end_time'])) {
                        $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['end_time']);
                    }
                }
            } elseif (!empty($schedule['date'])) {
                $allDatetimes[] = \Carbon\Carbon::parse($schedule['date']);
            }
        }

        if (empty($allDatetimes)) return 'upcoming';

        $startDate = min($allDatetimes);
        $endDate = max($allDatetimes);
        $now = now();

        if ($now->between($startDate, $endDate)) return 'ongoing';
        if ($now->greaterThan($endDate)) return 'completed';
        return 'upcoming';
    }

    private function validateCurrentStep()
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'name_en' => 'required|string|max:255',
                'name_id' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:events,slug,' . $this->event_id,
                'visibility' => 'required',
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validate([
                'description_en' => 'required',
                'description_id' => 'required',
            ]);
        } elseif ($this->currentStep === 3) {
            $rules = [
                'type' => 'required|in:offline,online,hybrid',
            ];

            if ($this->type !== 'online') {
                $rules['venue_en'] = 'required';
                $rules['venue_id'] = 'required';
            }

            if ($this->type !== 'offline') {
                $rules['platform'] = 'required';
                $rules['meeting_link'] = 'required|url';
                
                if ($this->platform === 'Zoom Meeting') {
                    $rules['meeting_info.meeting_id'] = 'required';
                    $rules['meeting_info.passcode'] = 'required';
                }
            }

            $this->validate($rules);
        } elseif ($this->currentStep === 4) {
             // Collaborators (Step 4)
             $this->validate([
                'personnel' => 'required|array',
             ]);
        } elseif ($this->currentStep === 5) {
             // Schedule (Step 5)
             $this->validate([
                'daily_schedules' => 'required|array|min:1',
                'daily_schedules.*.date' => 'required|date',
                'daily_schedules.*.agenda.*.start_time' => 'required',
                'daily_schedules.*.agenda.*.end_time' => 'required',
                'daily_schedules.*.agenda.*.title.en' => 'required',
            ]);
        } elseif ($this->currentStep === 6) {
            // Quota (Step 6)
            $this->validate([
                'quota' => [
                    'required',
                    'integer',
                    'min:0',
                    function ($attribute, $value, $fail) {
                        if ($this->registrant_limit != -1) {
                            if ($value == 0) {
                                $fail("Your current plan does not allow unlimited registrants. Maximum allowed is {$this->registrant_limit}.");
                            } elseif ($value > $this->registrant_limit) {
                                $fail("Your plan only allows a maximum of {$this->registrant_limit} registrants. Please upgrade your plan for more capacity.");
                            }
                        }
                    }
                ]
            ]);
            if ($this->is_paid_event) {
                $this->validate([
                    'ticket_tiers' => [
                        'required', 'array', 'min:1',
                        function ($attribute, $value, $fail) {
                            if ($this->quota > 0) {
                                $totalTierQuota = collect($value)->sum('quota');
                                if ($totalTierQuota > $this->quota) {
                                    $fail("The total quota for all tiers ({$totalTierQuota}) cannot exceed the Participant Quota ({$this->quota}). Please adjust the tier amounts.");
                                }
                            }
                        }
                    ],
                    'ticket_tiers.*.name' => 'required',
                    'ticket_tiers.*.price' => 'required|numeric|min:0',
                    'ticket_tiers.*.quota' => 'required|integer|min:1',
                    'ticket_tiers.*.sales_start_at' => 'required|date',
                    'ticket_tiers.*.sales_end_at' => 'required|date|after_or_equal:ticket_tiers.*.sales_start_at',
                ]);
            }
        } elseif ($this->currentStep === 7) {
            // Form (Step 7)
        } elseif ($this->currentStep === 8) {
            // Automation (Step 8)
            $this->validate(['confirmation_template_id' => 'nullable']);
        }
    }

    // Media Picker
    public function openFilePicker($target)
    {
        $this->activePickerTarget = $target;
        $this->showFilePicker = true;
        $this->dispatch('open-media-modal');
    }

    #[On('media-selected')]
    public function handleFileSelected($url)
    {
        $data = ['preview_url' => $url, 'path' => $url]; // Fallback path to URL if picker doesn't provide it
        if ($this->activePickerTarget === 'banner') {
            $this->existingBannerUrl = $data['preview_url'];
            $this->drive_banner_path = $data['path'];
            $this->banner = null;
        } elseif ($this->activePickerTarget === 'og_image') {
            $this->existingOgImageUrl = $data['preview_url'];
            $this->drive_og_image_path = $data['path'];
            $this->og_image = null;
        } elseif (str_starts_with($this->activePickerTarget, 'personnel.')) {
            $parts = explode('.', $this->activePickerTarget);
            $type = $parts[1];
            $index = $parts[2];
            if (isset($this->personnel[$type][$index])) {
                $this->personnel[$type][$index]['photo_url'] = $data['preview_url'];
                $this->personnel[$type][$index]['drive_photo_path'] = $data['path'];
            }
        } elseif (str_starts_with($this->activePickerTarget, 'sponsors.')) {
            $parts = explode('.', $this->activePickerTarget);
            $catIdx = $parts[1];
            $itemIdx = $parts[3];
            if (isset($this->sponsors[$catIdx]['items'][$itemIdx])) {
                $this->sponsors[$catIdx]['items'][$itemIdx]['logo_url'] = $data['preview_url'];
                $this->sponsors[$catIdx]['items'][$itemIdx]['drive_logo_path'] = $data['path'];
            }
        }
        $this->closeFilePicker();
    }

    public function closeFilePicker()
    {
        $this->showFilePicker = false;
        $this->activePickerTarget = null;
        $this->dispatch('close-modal', 'file-manager-picker');
    }

    // Dynamic Row Helpers
    public function addSchedule()
    {
        $this->daily_schedules[] = ['date' => '', 'agenda' => []];
    }

    public function updatedBanner()
    {
        $this->validate([
            'banner' => 'nullable|image|max:10240|mimes:jpeg,png,jpg,webp',
        ]);
    }

    public function updatedOgImage()
    {
        $this->validate([
            'og_image' => 'nullable|image|max:5120|mimes:jpeg,png,jpg,webp',
        ]);
    }

    public function removeSchedule($index)
    {
        unset($this->daily_schedules[$index]);
        $this->daily_schedules = array_values($this->daily_schedules);
    }

    public function updated($name, $value)
    {
        // Favicon auto-fetcher for personnel social links
        if (preg_match('/personnel\.(speakers|moderators)\.(\d+)\.social_links\.(\d+)\.url/', $name, $matches)) {
            $type = $matches[1];
            $pIdx = (int)$matches[2];
            $lIdx = (int)$matches[3];
            $this->personnel[$type][$pIdx]['social_links'][$lIdx]['favicon'] = $this->fetchFavicon($value);
        }

        // Auto-fill from Agenda
        if (preg_match('/^session_groups\.(\d+)\.sessions\.(\d+)\.event_agenda_id$/', $name, $matches)) {
            $groupIdx = (int)$matches[1];
            $sessionIdx = (int)$matches[2];
            
            if ($value) {
                $foundAgenda = null;
                $foundDate = null;
                foreach ($this->daily_schedules as $day) {
                    foreach ($day['agenda'] ?? [] as $agenda) {
                        if (($agenda['id'] ?? null) === $value) {
                            $foundAgenda = $agenda;
                            $foundDate = $day['date'] ?? '';
                            break 2;
                        }
                    }
                }

                if ($foundAgenda) {
                    $this->session_groups[$groupIdx]['sessions'][$sessionIdx]['title']['id'] = !empty($foundAgenda['title']['id']) ? $foundAgenda['title']['id'] : ($foundAgenda['title']['en'] ?? '');
                    $this->session_groups[$groupIdx]['sessions'][$sessionIdx]['title']['en'] = !empty($foundAgenda['title']['en']) ? $foundAgenda['title']['en'] : ($foundAgenda['title']['id'] ?? '');
                    $this->session_groups[$groupIdx]['sessions'][$sessionIdx]['description']['id'] = !empty($foundAgenda['description']['id']) ? $foundAgenda['description']['id'] : ($foundAgenda['description']['en'] ?? '');
                    $this->session_groups[$groupIdx]['sessions'][$sessionIdx]['description']['en'] = !empty($foundAgenda['description']['en']) ? $foundAgenda['description']['en'] : ($foundAgenda['description']['id'] ?? '');
                    
                    if ($foundDate && !empty($foundAgenda['start_time'])) {
                        $this->session_groups[$groupIdx]['sessions'][$sessionIdx]['start_time'] = \Carbon\Carbon::parse($foundDate . ' ' . $foundAgenda['start_time'])->format('Y-m-d\TH:i');
                    }
                    if ($foundDate && !empty($foundAgenda['end_time'])) {
                        $this->session_groups[$groupIdx]['sessions'][$sessionIdx]['end_time'] = \Carbon\Carbon::parse($foundDate . ' ' . $foundAgenda['end_time'])->format('Y-m-d\TH:i');
                    }

                    $room = '';
                    // First pass: look for exact/common location keys
                    foreach ($foundAgenda['extra_info'] ?? [] as $info) {
                        $key = strtolower($info['key'] ?? '');
                        if (in_array($key, ['venue', 'room', 'ruangan', 'lokasi', 'location', 'tempat', 'hall', 'space'])) {
                            $room = $info['value'] ?? '';
                            break;
                        }
                    }
                    
                    // Second pass: look for keys or values containing location terms
                    if (empty($room)) {
                        foreach ($foundAgenda['extra_info'] ?? [] as $info) {
                            $key = strtolower($info['key'] ?? '');
                            $val = strtolower($info['value'] ?? '');
                            if (str_contains($key, 'room') || str_contains($key, 'ruang') || str_contains($key, 'hall') || str_contains($key, 'venue') || str_contains($key, 'lokasi') || str_contains($key, 'location') ||
                                str_contains($val, 'room') || str_contains($val, 'ruang') || str_contains($val, 'hall') || str_contains($val, 'venue') || str_contains($val, 'lokasi') || str_contains($val, 'location') || str_contains($val, 'plenary')) {
                                $room = $info['value'] ?? '';
                                break;
                            }
                        }
                    }

                    // Third pass: fallback to the first extra_info item
                    if (empty($room) && !empty($foundAgenda['extra_info'])) {
                        $firstInfo = $foundAgenda['extra_info'][0];
                        if (!empty($firstInfo['key']) && !empty($firstInfo['value'])) {
                            $room = $firstInfo['key'] . ' - ' . $firstInfo['value'];
                        } else {
                            $room = !empty($firstInfo['value']) ? $firstInfo['value'] : ($firstInfo['key'] ?? '');
                        }
                    }
                    $this->session_groups[$groupIdx]['sessions'][$sessionIdx]['room_name'] = $room;
                }
            }
        }
    }

    private function fetchFavicon($url)
    {
        if (empty($url)) return null;
        try {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) $url = "https://" . $url;
            $host = parse_url($url, PHP_URL_HOST);
            if (!$host) return null;
            return "https://www.google.com/s2/favicons?domain={$host}&sz=64";
        } catch (\Exception $e) {
            return null;
        }
    }

    public function addAgenda($dayIndex)
    {
        $this->daily_schedules[$dayIndex]['agenda'][] = [
            'id' => 'session_' . uniqid(),
            'is_active' => true,
            'start_time' => '',
            'end_time' => '',
            'title' => ['en' => '', 'id' => ''],
            'description' => ['en' => '', 'id' => ''],
            'speaker_ids' => [],
            'moderator_ids' => [],
            'materials_link' => '',
            'extra_info' => []
        ];
    }

    public function removeAgenda($dayIndex, $agendaIndex)
    {
        unset($this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]);
        $this->daily_schedules[$dayIndex]['agenda'] = array_values($this->daily_schedules[$dayIndex]['agenda']);
    }

    public function addExtraInfo($dayIndex, $agendaIndex)
    {
        $this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]['extra_info'][] = ['key' => '', 'value' => ''];
    }

    public function removeExtraInfo($dayIndex, $agendaIndex, $infoIndex)
    {
        unset($this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]['extra_info'][$infoIndex]);
        $this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]['extra_info'] = array_values($this->daily_schedules[$dayIndex]['agenda'][$agendaIndex]['extra_info']);
    }

    public function addPersonnel($type)
    {
        $this->personnel[$type][] = [
            'id' => uniqid(), 
            'name' => '', 
            'organization' => '', 
            'photo_url' => '', 
            'social_links' => []
        ];
    }

    public function addSocialLink($type, $personIndex)
    {
        $this->personnel[$type][$personIndex]['social_links'][] = ['url' => '', 'favicon' => null];
    }

    public function removeSocialLink($type, $personIndex, $linkIndex)
    {
        unset($this->personnel[$type][$personIndex]['social_links'][$linkIndex]);
        $this->personnel[$type][$personIndex]['social_links'] = array_values($this->personnel[$type][$personIndex]['social_links']);
    }

    public function removePersonnel($type, $index)
    {
        unset($this->personnel[$type][$index]);
        $this->personnel[$type] = array_values($this->personnel[$type]);
    }

    public function addSponsorCategory()
    {
        $this->sponsors[] = ['category_name' => '', 'items' => []];
    }

    public function removeSponsorCategory($catIndex)
    {
        unset($this->sponsors[$catIndex]);
        $this->sponsors = array_values($this->sponsors);
    }

    public function addSponsorItem($catIndex)
    {
        $this->sponsors[$catIndex]['items'][] = ['name' => '', 'logo_url' => '', 'website' => ''];
    }

    public function removeSponsorItem($catIndex, $itemIndex)
    {
        unset($this->sponsors[$catIndex]['items'][$itemIndex]);
        $this->sponsors[$catIndex]['items'] = array_values($this->sponsors[$catIndex]['items']);
    }

    // Ticket Tiers Helpers
    public function addTicketTier()
    {
        // Smart defaults: Start now, end at the event end date if exists
        $allDatetimes = [];
        foreach ($this->daily_schedules as $schedule) {
            if (!empty($schedule['agenda'])) {
                foreach ($schedule['agenda'] as $agendaItem) {
                    $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['start_time']);
                    $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['end_time']);
                }
            } else if (!empty($schedule['date'])) {
                $allDatetimes[] = \Carbon\Carbon::parse($schedule['date']);
            }
        }
        $overallEndDate = !empty($allDatetimes) ? max($allDatetimes)->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i');

        $this->ticket_tiers[] = [
            'name' => '', 
            'price' => 0, 
            'quota' => 0, 
            'description' => '',
            'sales_start_at' => now()->format('Y-m-d\TH:i'),
            'sales_end_at' => $overallEndDate
        ];
    }

    public function removeTicketTier($index)
    {
        unset($this->ticket_tiers[$index]);
        $this->ticket_tiers = array_values($this->ticket_tiers);
    }

    public function addYoutubeRecording()
    {
        $this->youtube_recordings[] = ['title' => '', 'link' => ''];
    }

    public function removeYoutubeRecording($index)
    {
        unset($this->youtube_recordings[$index]);
        $this->youtube_recordings = array_values($this->youtube_recordings);
    }

    public function generateTemplate($field)
    {
        $mapping = [
            'confirmation_template_id' => 'transactional',
            'checkin_template_id'      => 'auto_checkin',
            'invoice_template_id'       => 'event_invoice',
            'reminder_template_id'      => 'reminder',
            'certificate_template_id'   => 'certificate',
            'feedback_template_id'      => 'event_feedback',
        ];

        if (!isset($mapping[$field])) return;

        $category = $mapping[$field];
        $library = \App\Models\EventEmailTemplate::getLibrary();
        
        if (!isset($library[$category])) return;

        $tplData = $library[$category];

        $template = \App\Models\EventEmailTemplate::create([
            'subject'          => $tplData['subject'],
            'content'          => $tplData['content'],
            'whatsapp_content' => $tplData['whatsapp_content'],
            'category'         => $category,
            'organizer_id'     => auth()->user()->organizer_id,
        ]);

        $this->{$field} = $template->id;

        // Refresh template lists
        $this->loadTemplates();

        $this->dispatch('swal:modal', [
            'type'  => 'success',
            'title' => 'MAGIC APPLIED!',
            'text'  => 'Professional template has been generated and assigned.',
        ]);
    }

    public function loadTemplates()
    {
        $this->confirmationTemplates = \App\Models\EventEmailTemplate::whereNull('event_id')->where('category', 'transactional')->get();
        $this->checkinTemplates      = \App\Models\EventEmailTemplate::whereNull('event_id')->where('category', 'auto_checkin')->get();
        $this->invoiceTemplates      = \App\Models\EventEmailTemplate::whereNull('event_id')->where('category', 'event_invoice')->get();
        $this->reminderTemplates     = \App\Models\EventEmailTemplate::whereNull('event_id')->where('category', 'reminder')->get();
        $this->certificateTemplates  = \App\Models\EventEmailTemplate::whereNull('event_id')->where('category', 'certificate')->get();
        $this->feedbackTemplates     = \App\Models\EventEmailTemplate::whereNull('event_id')->where('category', 'event_feedback')->get();
    }

    public function save()
    {
        // Final validation for everything
        $this->validate([
            'name_en' => 'required|string|max:255',
            'name_id' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug,' . $this->event_id,
            'quota' => 'required|integer|min:0',
            'banner' => 'nullable|image|max:10240|mimes:jpeg,png,jpg,webp',
            'og_image' => 'nullable|image|max:5120|mimes:jpeg,png,jpg,webp',
            'type' => 'required|in:offline,online,hybrid',
            'daily_schedules' => 'required|array|min:1',
        ]);

        // Process overall dates
        $allDatetimes = [];
        foreach ($this->daily_schedules as $schedule) {
            if (!empty($schedule['agenda'])) {
                foreach ($schedule['agenda'] as $agendaItem) {
                    $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['start_time']);
                    $allDatetimes[] = \Carbon\Carbon::parse($schedule['date'] . ' ' . $agendaItem['end_time']);
                }
            } else {
                $allDatetimes[] = \Carbon\Carbon::parse($schedule['date']);
            }
        }
        $overallStartDate = !empty($allDatetimes) ? min($allDatetimes) : now();
        $overallEndDate = !empty($allDatetimes) ? max($allDatetimes) : now();

        // Automatic Status Calculation
        $now = now();
        $computedStatus = 'upcoming';
        if ($now->between($overallStartDate, $overallEndDate)) {
            $computedStatus = 'ongoing';
        } elseif ($now->greaterThan($overallEndDate)) {
            $computedStatus = 'completed';
        }

        // 1. Process speakers uploads permanently
        if (!empty($this->personnel['speakers'])) {
            foreach ($this->personnel['speakers'] as $idx => $person) {
                if (isset($this->speaker_uploads[$idx]) && $this->speaker_uploads[$idx] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    $path = $this->speaker_uploads[$idx]->store('personnel/photos', 'public');
                    $this->personnel['speakers'][$idx]['photo_url'] = Storage::disk('public')->url($path);
                }
            }
        }

        // 2. Process moderators uploads permanently
        if (!empty($this->personnel['moderators'])) {
            foreach ($this->personnel['moderators'] as $idx => $person) {
                if (isset($this->moderator_uploads[$idx]) && $this->moderator_uploads[$idx] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    $path = $this->moderator_uploads[$idx]->store('personnel/photos', 'public');
                    $this->personnel['moderators'][$idx]['photo_url'] = Storage::disk('public')->url($path);
                }
            }
        }

        // 3. Process sponsors logo uploads permanently
        if (!empty($this->sponsors)) {
            foreach ($this->sponsors as $catIdx => $category) {
                if (!empty($category['items'])) {
                    foreach ($category['items'] as $itemIdx => $item) {
                        $key = "{$catIdx}_{$itemIdx}";
                        if (isset($this->sponsor_uploads[$key]) && $this->sponsor_uploads[$key] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            $path = $this->sponsor_uploads[$key]->store('sponsors/logos', 'public');
                            $this->sponsors[$catIdx]['items'][$itemIdx]['logo_url'] = Storage::disk('public')->url($path);
                        }
                    }
                }
            }
        }

        $data = [
            'name' => ['en' => $this->name_en, 'id' => $this->name_id],
            'theme' => ['en' => $this->theme_en, 'id' => $this->theme_id],
            'slug' => $this->slug,
            'description' => ['en' => $this->description_en, 'id' => $this->description_id],
            'start_date' => $overallStartDate,
            'end_date' => $overallEndDate,
            'quota' => $this->quota,
            'is_active' => (bool)$this->is_active,
            'status' => $computedStatus,
            'visibility' => $this->visibility,
            'requires_account' => (bool)$this->requires_account,
            'is_paid_event' => $this->is_paid_event,
            'fee_payer' => $this->fee_payer,
            'payment_expiry_duration' => $this->payment_expiry_duration,
            'type' => $this->type,
            'daily_schedules' => $this->daily_schedules,
            'personnel' => $this->personnel,
            'sponsors' => $this->sponsors,
            'inquiry_form_id' => $this->inquiry_form_id ?: null,
            'confirmation_template_id' => $this->confirmation_template_id ?: null,
            'checkin_template_id' => $this->checkin_template_id ?: null,
            'invoice_template_id' => $this->invoice_template_id ?: null,
            'reminder_template_id' => $this->reminder_template_id ?: null,
            'certificate_template_id' => $this->certificate_template_id ?: null,
            'feedback_template_id' => $this->feedback_template_id ?: null,
            'feedback_form_id' => $this->feedback_form_id ?: null,
            'is_feedback_active' => (bool)$this->is_feedback_active,
            'external_registration_link' => $this->use_external_link ? $this->external_registration_link : null,
            'partnership_link' => $this->use_partnership_link ? $this->partnership_link : null,
            'youtube_recordings' => $this->youtube_recordings,
            'organizer_id' => $this->organizer_id ?: null,
            'field_config' => $this->field_config,
        ];

        if ($this->type === 'offline' || $this->type === 'hybrid') {
            $data['venue'] = ['en' => $this->venue_en, 'id' => $this->venue_id];
            $data['google_maps_iframe'] = $this->google_maps_iframe;
        }

        if ($this->type === 'online' || $this->type === 'hybrid') {
            $data['platform'] = $this->platform;
            $data['meeting_link'] = $this->meeting_link;
            $data['meeting_info'] = $this->meeting_info;
        }

        if ($this->isEditMode) {
            $event = Event::findOrFail($this->event_id);
            $event->update($data);
        } else {
            $event = Event::create($data);
        }

        // --- Sync Ticket Tiers ---
        if ($this->is_paid_event) {
            $existingIds = array_filter(array_column($this->ticket_tiers, 'id'));
            $event->ticketTiers()->whereNotIn('id', $existingIds)->delete();

            foreach ($this->ticket_tiers as $tierData) {
                if (isset($tierData['id'])) {
                    $event->ticketTiers()->where('id', $tierData['id'])->update([
                        'name' => $tierData['name'],
                        'price' => $tierData['price'],
                        'quota' => $tierData['quota'],
                        'description' => $tierData['description'] ?? null,
                        'sales_start_at' => $tierData['sales_start_at'] ? \Carbon\Carbon::parse($tierData['sales_start_at']) : null,
                        'sales_end_at' => $tierData['sales_end_at'] ? \Carbon\Carbon::parse($tierData['sales_end_at']) : null,
                    ]);
                } else {
                    $event->ticketTiers()->create($tierData);
                }
            }
        } else {
            $event->ticketTiers()->delete();
        }

        // --- Sync Session Groups & Sessions ---
        $existingGroupIds = array_filter(array_column($this->session_groups, 'id'));
        $event->sessionGroups()->whereNotIn('id', $existingGroupIds)->delete();

        foreach ($this->session_groups as $groupData) {
            if (isset($groupData['id']) && $groupData['id']) {
                $group = EventSessionGroup::findOrFail($groupData['id']);
                $group->update([
                    'name' => $groupData['name'],
                    'selection_type' => $groupData['selection_type'],
                    'is_required' => (bool)$groupData['is_required'],
                ]);
            } else {
                $group = $event->sessionGroups()->create([
                    'name' => $groupData['name'],
                    'selection_type' => $groupData['selection_type'],
                    'is_required' => (bool)$groupData['is_required'],
                ]);
            }

            // Sync Sessions inside this Group
            $existingSessionIds = array_filter(array_column($groupData['sessions'], 'id'));
            $group->sessions()->whereNotIn('id', $existingSessionIds)->delete();

            foreach ($groupData['sessions'] as $sessionData) {
                $sessionPayload = [
                    'event_agenda_id' => $sessionData['event_agenda_id'] ?: null,
                    'title' => $sessionData['title'],
                    'description' => $sessionData['description'] ?? null,
                    'room_name' => $sessionData['room_name'] ?? null,
                    'start_time' => $sessionData['start_time'] ? \Carbon\Carbon::parse($sessionData['start_time']) : null,
                    'end_time' => $sessionData['end_time'] ? \Carbon\Carbon::parse($sessionData['end_time']) : null,
                    'quota' => (int)($sessionData['quota'] ?? -1),
                    'is_checkin_active' => (bool)($sessionData['is_checkin_active'] ?? false),
                ];

                if (isset($sessionData['id']) && $sessionData['id']) {
                    $group->sessions()->where('id', $sessionData['id'])->update($sessionPayload);
                } else {
                    $group->sessions()->create($sessionPayload);
                }
            }
        }

        // Final Media Processing
        if ($this->drive_banner_path) {
            $event->clearMediaCollection();
            $event->addMediaFromDisk($this->drive_banner_path, 'google')->preservingOriginal()->toMediaCollection();
        } elseif ($this->banner) {
            $event->clearMediaCollection();
            $event->addMedia($this->banner->getRealPath())->toMediaCollection();
        }

        // --- Process OG Image ---
        if ($this->drive_og_image_path) {
            $event->clearMediaCollection('og_image');
            $event->addMediaFromDisk($this->drive_og_image_path, 'google')->preservingOriginal()->toMediaCollection('og_image');
        } elseif ($this->og_image) {
            $event->clearMediaCollection('og_image');
            $event->addMedia($this->og_image->getRealPath())->toMediaCollection('og_image');
        }

        session()->flash('message', 'Event blueprint authorized successfully.');
        return $this->redirect(route('admin.events.index'), navigate: true);
    }

    public function updatedSpeakerUploads($file, $index)
    {
        $this->validate([
            "speaker_uploads.$index" => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
        ]);

        if ($file) {
            $this->personnel['speakers'][$index]['photo_url'] = $file->temporaryUrl();
        }
    }

    public function updatedModeratorUploads($file, $index)
    {
        $this->validate([
            "moderator_uploads.$index" => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
        ]);

        if ($file) {
            $this->personnel['moderators'][$index]['photo_url'] = $file->temporaryUrl();
        }
    }

    public function updatedSponsorUploads($file, $key)
    {
        $this->validate([
            "sponsor_uploads.$key" => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
        ]);

        if ($file) {
            $parts = explode('_', $key);
            $catIdx = $parts[0];
            $itemIdx = $parts[1];
            $this->sponsors[$catIdx]['items'][$itemIdx]['logo_url'] = $file->temporaryUrl();
        }
    }

    public function render()
    {
        $agendas = [];
        foreach ($this->daily_schedules as $dayIdx => $day) {
            $dateStr = $day['date'] ?? '';
            foreach ($day['agenda'] ?? [] as $agendaIdx => $agenda) {
                // Only include enabled sessions
                if (!($agenda['is_active'] ?? true)) {
                    continue;
                }

                // Resolve title (could be array or string)
                $title = '';
                if (isset($agenda['title'])) {
                    if (is_array($agenda['title'])) {
                        $title = !empty($agenda['title']['id']) ? $agenda['title']['id'] : (!empty($agenda['title']['en']) ? $agenda['title']['en'] : '');
                    } else {
                        $title = $agenda['title'];
                    }
                }

                $agendas[] = [
                    'id' => $agenda['id'] ?? null,
                    'title' => $title,
                    'start_time' => $agenda['start_time'] ?? '',
                    'end_time' => $agenda['end_time'] ?? '',
                    'date' => $dateStr,
                ];
            }
        }

        return view('livewire.admin.event.form', [
            'eventAgendas' => $agendas,
        ])->layout('layouts.app');
    }
}

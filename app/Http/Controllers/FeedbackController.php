<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\FeedbackSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Menampilkan halaman form feedback kepada peserta.
     */
    public function show($event, $registration)
    {
        $event = Event::withoutGlobalScope('organizer')
            ->where(function($query) use ($event) {
                $query->where('id', $event)->orWhere('slug', $event);
            })
            ->with('feedbackForm') // Eager load form
            ->firstOrFail(['id', 'slug', 'name', 'feedback_form_id', 'is_feedback_active']);

        $registration = Registration::withoutGlobalScope('organizer')
            ->where('uuid', $registration)
            ->firstOrFail(['id', 'uuid', 'checked_in_at']);

        // Validasi 1: Apakah fitur feedback untuk event ini aktif?
        if (!$event->is_feedback_active || !$event->feedbackForm) {
            abort(404, 'Feedback for this event is not available.');
        }

        // Validasi 2: Apakah peserta sudah check-in?
        if (!$registration->checked_in_at) {
            return view('feedback.error', ['message' => 'You must check in to the event before providing feedback.']);
        }

        // Validasi 3: Apakah peserta sudah pernah mengisi feedback sebelumnya?
        $existingSubmission = FeedbackSubmission::where('registration_id', $registration->id)->exists();
        if ($existingSubmission) {
            return view('feedback.error', ['message' => 'You have already submitted your feedback for this event. Thank you!']);
        }

        // Jika semua validasi lolos, tampilkan form
        $formFields = collect($event->feedbackForm->fields)->map(function ($field) {
            // Jika tipenya select/radio dan options adalah string, ubah menjadi array
            if (in_array($field['type'], ['select', 'radio']) && is_string($field['options'])) {
                // Pisahkan string berdasarkan koma, trim spasi, dan hapus entri kosong
                $field['options'] = array_filter(array_map('trim', explode(',', $field['options'])));
            }
            return $field;
        })->all();

        return view('feedback.form', [
            'event' => $event,
            'registration' => $registration,
            'formFields' => $formFields // Kirim data yang sudah diolah
        ]);
    }

    /**
     * Menyimpan data feedback yang disubmit oleh peserta.
     */
    public function store(Request $request, $event, $registration)
    {
        $event = Event::withoutGlobalScope('organizer')
            ->where(function($query) use ($event) {
                $query->where('id', $event)->orWhere('slug', $event);
            })
            ->with('feedbackForm')
            ->firstOrFail(['id', 'slug', 'feedback_form_id', 'is_feedback_active']);

        $registration = Registration::withoutGlobalScope('organizer')
            ->where('uuid', $registration)
            ->firstOrFail(['id', 'uuid']);

        // Lakukan lagi validasi untuk keamanan
        if (!$event->is_feedback_active || !$event->feedbackForm || FeedbackSubmission::where('registration_id', $registration->id)->exists()) {
            abort(403, 'Submission is not allowed.');
        }

        $formFields = $event->feedbackForm->fields;
        $rules = [];

        // Buat aturan validasi secara dinamis dari struktur form
        foreach ($formFields as $field) {
            if ($field['type'] === 'section') continue;
            
            if (!empty($field['required'])) {
                $rules[$field['name']] = 'required';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Simpan data ke database
        FeedbackSubmission::create([
            'feedback_form_id' => $event->feedback_form_id,
            'registration_id' => $registration->id,
            'data' => $validator->validated()
        ]);

        return view('feedback.success', [
            'event' => $event,
            'message' => 'Thank you! Your feedback has been submitted successfully.'
        ]);
    }
}

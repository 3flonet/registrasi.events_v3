<?php

namespace App\Exports;

use App\Models\Event;
use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EventParticipantsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $event;
    protected $customFields;

    public function __construct($eventId)
    {
        $this->event = Event::with('inquiryForm')->findOrFail($eventId);
        $this->customFields = $this->event->inquiryForm ? $this->event->inquiryForm->fields : [];
    }

    public function collection()
    {
        return Registration::where('event_id', $this->event->id)
            ->with(['submission', 'ticketTier'])
            ->get();
    }

    public function headings(): array
    {
        $headings = [
            'Nama',
            'Email',
            'No. WhatsApp',
            'Sumber',
            'Tipe Kehadiran',
            'Ticket Tier',
            'Status Pembayaran',
        ];

        foreach ($this->customFields as $field) {
            $headings[] = $field['label'];
        }

        $headings[] = 'Waktu Daftar';

        return $headings;
    }

    public function map($registration): array
    {
        $isInvitation = $registration->submission && $registration->submission->invitation_id;
        $submissionData = $registration->submission ? $registration->submission->data : ($registration->data ?: []);

        $row = [
            $registration->name,
            $registration->email,
            $registration->phone_number,
            $isInvitation ? 'Undangan' : 'Publik',
            $registration->attendance_type ?: 'In-Person',
            $registration->ticketTier ? $registration->ticketTier->name : 'General Admission',
            strtoupper($registration->payment_status ?: 'paid'),
        ];

        foreach ($this->customFields as $field) {
            $fieldName = $field['name'];
            $val = $submissionData[$fieldName] ?? '-';
            
            // Logika Media (Signature/Image/File)
            if ($registration->submission && (
                $field['type'] === 'signature' || 
                $field['type'] === 'image' || 
                $field['type'] === 'file' || 
                $val === '[Digital Signature Attached]' || 
                $val === '[File Attached]'
            )) {
                $media = $registration->submission->getMedia('attachments')->first(function($m) use ($fieldName, $field) {
                    $search = strtolower($fieldName);
                    $mName = strtolower($m->name);
                    $fName = strtolower($m->file_name);
                    
                    // Match by name
                    if (str_contains($mName, $search) || str_contains($fName, $search)) return true;
                    
                    // Fallback by type
                    if ($field['type'] === 'signature' && str_contains($mName, 'signature')) return true;
                    if ($field['type'] === 'image' && str_contains($m->mime_type, 'image')) return true;
                    if ($field['type'] === 'file' && !str_contains($m->mime_type, 'image')) return true;

                    return false;
                });

                if ($media) {
                    $val = $media->getUrl();
                }
            }

            if (is_array($val)) {
                $val = implode(', ', $val);
            }
            $row[] = $val;
        }

        $row[] = $registration->created_at->format('Y-m-d H:i:s');

        return $row;
    }
}

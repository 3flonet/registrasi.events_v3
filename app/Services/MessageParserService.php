<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\Event;
use App\Models\EventEmailTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class MessageParserService
{
    /**
     * Parse content specifically for WhatsApp (Plain text + Attachment URL)
     */
    public function parseForWhatsApp(string $content, Registration $registration, ?EventEmailTemplate $template = null): array
    {
        $qrUrl = 'https://quickchart.io/qr?text=' . urlencode(route('checkin.scan', $registration->uuid)) . '&size=250&margin=1';
        $ticketPageUrl = route('tickets.qrcode', $registration->uuid);
        
        // 1. Check if QR code tag exists
        $hasQrCode = str_contains($content, '{ticket_qrcode}');
        
        // 2. Temporarily replace {ticket_qrcode} with empty string for the message body
        $cleanContent = str_replace('{ticket_qrcode}', '', $content);
        
        // 3. Parse other tags normally
        $parsedMessage = $this->parse($cleanContent, $registration, $template);
        
        // 4. Clean up any leftover HTML tags (WhatsApp is plain text)
        $parsedMessage = strip_tags($parsedMessage);
        // Convert some HTML entities if any
        $parsedMessage = html_entity_decode($parsedMessage);

        return [
            'message' => trim($parsedMessage),
            'attachment_url' => $hasQrCode ? $qrUrl : null,
            'fallback_url' => $hasQrCode ? $ticketPageUrl : null
        ];
    }

    /**
     * Parse template content with registration and event data.
     */
    public function parse(string $content, Registration $registration, ?EventEmailTemplate $template = null): string
    {
        $event = $registration->event;
        $organizer = $event->organizer;

        $placeholders = [
            // Peserta
            '{name}'             => $registration->name,
            '{{ nama_peserta }}' => $registration->name,
            '{email}'            => $registration->email,
            '{phone}'            => $registration->phone_number,
            '{{ tipe_kehadiran }}' => ucfirst($registration->attendance_type ?? $event->type),
            '{{ total_bayar }}'  => 'Rp ' . number_format($registration->total_price, 0, ',', '.'),
            
            // Event Details
            '{event_name}'       => $event->name,
            '{{ nama_acara }}'   => $event->name,
            '{event_type}'       => match($event->type) {
                'offline' => 'Physical (Offline)',
                'online'  => 'Virtual (Online)',
                'hybrid'  => 'Hybrid (Offline & Online)',
                default   => ucfirst($event->type)
            },
            '{venue}'            => $event->venue ?? 'N/A',
            '{platform}'         => $event->platform ?? 'Zoom/Google Meet',
            '{meeting_link}'     => $event->meeting_link ?? '#',
            
            // Smart Instruction based on attendee's choice
            '{event_instruction}' => match($registration->attendance_type) {
                'online'  => "Silakan bergabung secara virtual melalui " . ($event->platform ?? 'tautan') . " berikut: " . ($event->meeting_link ?? '-'),
                'offline' => "Silakan datang langsung ke lokasi acara di: " . ($event->venue ?? 'Lokasi segera dikonfirmasi'),
                default   => "Detail kehadiran akan dikirimkan melalui email terpisah."
            },

            '{date}'             => Carbon::parse($event->start_date)->translatedFormat('d M Y'),
            '{time}'             => Carbon::parse($event->start_date)->format('H:i'),
            '{checkin_time}'     => $registration->checked_in_at ? Carbon::parse($registration->checked_in_at)->format('H:i') : now()->format('H:i'),
            '{location}'         => $event->venue ?? 'Online / Virtual',
            
            // Links & Codes
            '{link_ticket}'      => route('tickets.qrcode', $registration->uuid),
            '{link_invoice}'     => route('invoice.show', $registration->uuid),
            '{payment_link}'     => route('invoice.show', $registration->uuid),
            '{link_certificate}' => ($registration->checked_in_at || $registration->checkinLogs()->count() > 0) ? route('public.certificate.show', $registration->uuid) : '-',
            '[link_sertifikat]'  => ($registration->checked_in_at || $registration->checkinLogs()->count() > 0) ? route('public.certificate.show', $registration->uuid) : '-',
            '{link_feedback}'    => $event->is_feedback_active ? route('feedback.show', ['event' => $event, 'registration' => $registration->uuid]) : '#',
            '[link_feedback]'    => $event->is_feedback_active ? route('feedback.show', ['event' => $event, 'registration' => $registration->uuid]) : '#',
            '{ticket_code}'      => strtoupper(substr($registration->uuid, 0, 8)),
            '{ticket_qrcode}'    => '<img src="https://quickchart.io/qr?text=' . urlencode(route('checkin.scan', $registration->uuid)) . '&size=200&margin=1" style="width: 180px; height: 180px; display: block; margin: 20px auto; border: 10px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);" alt="QR Code">',
            
            // Financial (For Invoices)
            '{total_bayar}'      => 'Rp ' . number_format($registration->total_price, 0, ',', '.'),
            '{ticket_tier}'      => $registration->ticketTier->name ?? 'Standard',
            
            // Global
            '{app_name}'         => config('app.name'),
            '{{ app_name }}'     => config('app.name'),
            '{organizer}'        => $organizer->name ?? config('app.name'),

            // New: Jabatan & Company for Registration Context (Deep Search)
            '{jabatan}'          => $this->extractFromRegistration($registration, ['jabatan', 'job_title', 'category', 'position', 'profesi']),
            '{company}'          => $this->extractFromRegistration($registration, ['company', 'instansi', 'perusahaan', 'organisasi', 'institute', 'organization', 'instansi_perusahaan', 'nama_instansi']),
        ];

        // Banner Placeholder
        if ($template && $template->banner_path) {
            $bannerUrl = Storage::disk('public')->url($template->banner_path);
            $placeholders['{banner}'] = '<img src="'.$bannerUrl.'" style="width: 100%; height: auto; display: block; border-radius: 12px; margin-bottom: 20px;" alt="Banner">';
        } else {
            $placeholders['{banner}'] = '';
        }

        return str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $content
        );
    }

    /**
     * Parse invitation content with invitation and event data.
     */
    public function parseInvitation(string $content, \App\Models\Invitation $invitation): string
    {
        $event = $invitation->event;
        $organizer = $event->organizer;

        $placeholders = [
            '{name}'            => $invitation->name,
            '{company}'         => $invitation->company ?? 'Instansi',
            '{jabatan}'         => $invitation->category ?? 'Personal',
            '{category}'        => $invitation->category ?? 'Guest',
            '{event_name}'      => $event->getTranslation('name', 'en'),
            '{date}'            => Carbon::parse($event->start_date)->translatedFormat('d M Y'),
            '{venue}'           => $event->getTranslation('venue', 'en') ?? 'N/A',
            '{link_surat}'      => route('invitation.letter', $invitation->uuid),
            '{link_konfirmasi}' => route('invitation.confirm', $invitation->uuid),
            '{btn_surat}'       => '<a href="'.route('invitation.letter', $invitation->uuid).'" style="display: inline-block; padding: 14px 28px; background-color: #1a1235; color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 800; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; font-size: 11px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">Lihat Surat Undangan</a>',
            '{btn_konfirmasi}'  => '<a href="'.route('invitation.confirm', $invitation->uuid).'" style="display: inline-block; padding: 14px 28px; background-color: #4f46e5; color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 800; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; font-size: 11px; box-shadow: 0 4px 6px rgba(49, 46, 129, 0.2);">Konfirmasi Kehadiran</a>',
            '{organizer}'       => $organizer->name ?? config('app.name'),
        ];

        // Handle Digital Attachments (Lampiran)
        $attachmentLinks = '';
        if ($event->invitation_files && is_array($event->invitation_files)) {
            $links = [];
            foreach ($event->invitation_files as $path) {
                $links[] = asset('storage/' . $path);
            }
            $attachmentLinks = implode("\n", $links);
        }
        $placeholders['{link_lampiran}'] = $attachmentLinks;

        // Handle Button Attachments (Lampiran dalam bentuk Tombol untuk Email)
        $attachmentButtons = '';
        if ($event->invitation_files && is_array($event->invitation_files)) {
            $btns = [];
            foreach ($event->invitation_files as $path) {
                $filename = basename($path);
                $url = asset('storage/' . $path);
                $btns[] = '<a href="'.$url.'" style="display: inline-block; padding: 10px 20px; background-color: #6366f1; color: #ffffff; text-decoration: none; border-radius: 10px; font-weight: 800; font-family: sans-serif; text-transform: uppercase; letter-spacing: 0.5px; font-size: 10px; margin: 5px; box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);">Download: '.$filename.'</a>';
            }
            $attachmentButtons = '<div style="margin: 20px 0; text-align: center;">' . implode(' ', $btns) . '</div>';
        }
        $placeholders['{btn_lampiran}'] = $attachmentButtons;

        $content = str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $content
        );

        // Final Touch: Convert Quill alignment classes to inline styles for Email compatibility
        $content = str_replace('class="ql-align-justify"', 'style="text-align: justify;"', $content);
        $content = str_replace('class="ql-align-center"', 'style="text-align: center;"', $content);
        $content = str_replace('class="ql-align-right"', 'style="text-align: right;"', $content);

        return $content;
    }

    /**
     * Deep search for a field in registration data or submission data.
     */
    protected function extractFromRegistration(Registration $registration, array $keys): string
    {
        // 1. Check in primary registration data
        foreach ($keys as $key) {
            if (!empty($registration->data[$key])) {
                return $registration->data[$key];
            }
        }

        // 2. Check in linked inquiry submission data
        if ($registration->submission && !empty($registration->submission->data)) {
            foreach ($keys as $key) {
                if (!empty($registration->submission->data[$key])) {
                    return $registration->submission->data[$key];
                }
            }
        }

        // 3. FALLBACK: Check in original Invitation data (if this registration came from an invitation)
        // Note: We search for an invitation that matches the email and event
        $invitation = \App\Models\Invitation::where('event_id', $registration->event_id)
            ->where('email', $registration->email)
            ->first();

        if ($invitation) {
            // Check specific columns first
            if (in_array('company', $keys) && !empty($invitation->company)) {
                return $invitation->company;
            }
            if (in_array('jabatan', $keys) && !empty($invitation->category)) { // Invitation uses 'category' for jabatan
                return $invitation->category;
            }
        }

        return '-';
    }

    /**
     * Map registration parameters to values for Meta template component.
     */
    public function parseParameters(array $paramKeys, Registration $registration): array
    {
        $parsed = [];
        foreach ($paramKeys as $key) {
            $parsed[] = $this->parsePlaceholder($key, $registration);
        }
        return $parsed;
    }

    protected function parsePlaceholder($key, Registration $registration)
    {
        $event = $registration->event;
        
        switch ($key) {
            case 'name':
                return $registration->name;
            case 'event_name':
                return $event->name;
            case 'ticket_code':
                return strtoupper(substr($registration->uuid, 0, 8));
            case 'event_instruction':
                return match($registration->attendance_type) {
                    'online'  => "Silakan bergabung secara virtual melalui " . ($event->platform ?? 'tautan') . " berikut: " . ($event->meeting_link ?? '-'),
                    'offline' => "Silakan datang langsung ke lokasi acara di: " . ($event->venue ?? 'Lokasi segera dikonfirmasi'),
                    default   => "Detail kehadiran akan dikirimkan melalui email terpisah."
                };
            case 'date':
                return Carbon::parse($event->start_date)->translatedFormat('d M Y');
            case 'time':
                return Carbon::parse($event->start_date)->format('H:i');
            case 'location':
                return $event->venue ?? 'Online / Virtual';
            case 'total_bayar':
                return 'Rp ' . number_format($registration->total_price, 0, ',', '.');
            case 'payment_link':
            case 'ticket_url':
                return route('invoice.show', $registration->uuid);
            case 'link_certificate':
                return ($registration->checked_in_at || $registration->checkinLogs()->count() > 0) ? route('public.certificate.show', $registration->uuid) : '-';
            case 'link_feedback':
                return $event->is_feedback_active ? route('feedback.show', ['event' => $event, 'registration' => $registration->uuid]) : '#';
            case 'ticket_pdf':
                return route('tickets.qrcode', $registration->uuid);
            default:
                return $this->extractFromRegistration($registration, [$key]) ?: '-';
        }
    }
}

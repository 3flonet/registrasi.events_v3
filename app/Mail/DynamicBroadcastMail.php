<?php

namespace App\Mail;

use App\Models\EventEmailTemplate;
use App\Models\Registration;
use App\Traits\HasDynamicSmtp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailables\Attachment;

class DynamicBroadcastMail extends Mailable
{
    use Queueable, SerializesModels, HasDynamicSmtp;

    public EventEmailTemplate $template;
    public Registration $registration;
    protected ?string $qrCodeTempPath = null;

    /**
     * Create a new message instance.
     */
    public function __construct(EventEmailTemplate $template, Registration $registration)
    {
        $this->template = $template;
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // 1. Dapatkan Organizer
        $organizer = $this->registration->event->organizer;
        
        // 2. Terapkan SMTP Dinamis
        $this->applyOrganizerSmtp($organizer);

        // 3. Tentukan Alamat Pengirim (Menggunakan helper dari Trait yang peka terhadap Smart Fallback)
        $fromAddress = $this->getDynamicFromAddress($organizer);
        $fromName = $this->getDynamicFromName($organizer);

        \Illuminate\Support\Facades\Log::info('Mailable Envelope prepared', [
            'from' => $fromAddress,
            'subject' => $this->template->subject
        ]);

        // Proses placeholder sederhana untuk subjek
        $processedSubject = $this->processSimplePlaceholders($this->template->subject);

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName),
            subject: $processedSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        // 1. Proses semua placeholder untuk subjek dan konten
        $processedSubject = $this->processSimplePlaceholders($this->template->subject);
        $content = $this->template->content;

        // Auto-detect if content is plain text and needs line breaks converted to HTML
        // We check for common structural tags. If none found, convert newlines to <br>.
        if (!preg_match('/<(p|div|table|html|body|h[1-6]|br)/i', $content)) {
            $content = str_replace(["\r\n", "\r", "\n"], "<br>", $content);
        }

        $processedContent = $this->processSimplePlaceholders($content);
        $processedContent = $this->processConditionalPlaceholders($processedContent);

        // 2. Dapatkan data Organizer dan Event
        $event = $this->registration->event;
        $organizer = $event->organizer;

        // 3. Tentukan Logo (Prioritas: Organizer Logo -> Global App Logo)
        $logoPath = null;
        if ($organizer && $organizer->logo_path) {
            $logoPath = storage_path('app/public/' . $organizer->logo_path);
        } elseif (config('settings.app_logo')) {
            $logoPath = storage_path('app/public/' . config('settings.app_logo'));
        }

        $bannerPath = $this->template->banner_path
            ? storage_path('app/public/' . $this->template->banner_path)
            : null;

        // 3. Terapkan SMTP Dinamis dari Trait (Sudah dipanggil di envelope, tapi tidak apa-apa dipanggil lagi)
        $this->applyOrganizerSmtp($organizer);

        // 4. Nama Aplikasi / Organizer
        $appName = $organizer ? $organizer->name : config('app.name');

        // 5. Tentukan Logo (Prioritas: Organizer Logo -> Global App Logo)

        // 5. Bangun email, atur subjek, dan kirim data ke view
        return $this->subject($processedSubject)
            ->view('emails.layouts.broadcast', [
                'subject' => $processedSubject,
                'content' => $processedContent,
                'appName' => $appName,
                'logoPath' => ($logoPath && file_exists($logoPath)) ? $logoPath : null,
                'bannerPath' => ($bannerPath && file_exists($bannerPath)) ? $bannerPath : null,
                'primaryColor' => '#3b82f6',
                'secondaryColor' => '#1e293b',
            ]);
    }

    // Method __destruct akan otomatis dipanggil setelah objek tidak lagi digunakan (email terkirim)
    public function __destruct()
    {
        // Hapus file QR code sementara jika ada
        if ($this->qrCodeTempPath && file_exists($this->qrCodeTempPath)) {
            unlink($this->qrCodeTempPath);
        }
    }


    /**
     * Proses placeholder menggunakan central MessageParserService.
     */
    private function processSimplePlaceholders(string $text): string
    {
        $parser = app(\App\Services\MessageParserService::class);
        return $parser->parse($text, $this->registration, $this->template);
    }

    /**
     * Proses placeholder blok kondisional seperti [gambar_qr_code].
     */
    private function processConditionalPlaceholders(string $content): string
    {
        $attendanceType = $this->registration->attendance_type ?? $this->registration->event->type;
        $event = $this->registration->event;

        // Siapkan HTML untuk blok QR Code dengan narasi yang benar
        // PERUBAHAN: Menggunakan cid:qrcode.png yang berasal dari method attachments()
        $qrCodeHtml = '<div style="text-align: center; padding: 20px 0 30px 0;">' .
            '<p style="margin: 0 0 15px 0; color: #333333;">To speed up the check-in process at the venue, please have the QR Code below ready for our staff to scan. You can also access it via the button below.</p>' .
            '<img src="cid:qrcode.png" alt="QR Code Ticket" style="display: inline-block;">' .
            '</div>';

        $replacements = [
            '[tanggal_acara]' => view('emails.partials._date-format', ['event' => $event])->render(),
            '[info_acara_online]' => $attendanceType !== 'offline' ? view('emails.partials._online-info', ['event' => $event])->render() : '',
            '[info_lokasi_offline]' => $attendanceType === 'offline' ? view('emails.partials._offline-info', ['event' => $event])->render() : '',
            '[tombol_lihat_tiket]' => $attendanceType === 'offline' ? view('emails.partials._ticket-button', ['registration' => $this->registration])->render() : '',
            '[gambar_qr_code]' => $attendanceType === 'offline' ? $qrCodeHtml : '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }


    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];
        $attendanceType = $this->registration->attendance_type ?? $this->registration->event->type;

        if ($attendanceType === 'offline' && str_contains($this->template->content, '[gambar_qr_code]')) {

            $directory = storage_path('app/public/qrcodes');
            if (!file_exists($directory)) {
                mkdir($directory, 0775, true);
            }

            $this->qrCodeTempPath = storage_path('app/public/qrcodes/' . Str::random(40) . '.png');
            
            // Gunakan QuickChart API untuk menghasilkan PNG tanpa Imagick
            $url = route('checkin.scan', $this->registration->uuid);
            $qrChartUrl = "https://quickchart.io/qr?text=" . urlencode($url) . "&size=250&margin=1";
            
            // Simpan gambar dari API ke path sementara
            file_put_contents($this->qrCodeTempPath, file_get_contents($qrChartUrl));

            // Lampiran ini akan di-embed dengan Content-ID (cid) 'qrcode.png'
            $attachments[] = Attachment::fromPath($this->qrCodeTempPath)
                ->as('qrcode.png')
                ->withMime('image/png');
        }

        return $attachments;
    }
}

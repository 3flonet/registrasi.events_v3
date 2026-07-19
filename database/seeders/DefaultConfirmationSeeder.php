<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventEmailTemplate;
use App\Models\Event;

class DefaultConfirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Template Konfirmasi Default
        $template = EventEmailTemplate::updateOrCreate(
            ['subject' => 'Registration Confirmation - {event_name}'],
            [
                'content' => '
                    <div style="font-family: sans-serif; line-height: 1.6; color: #333;">
                        <h2>Halo {name}!</h2>
                        <p>Terima kasih telah mendaftar untuk acara <strong>{event_name}</strong>.</p>
                        <p>Pendaftaran Anda telah kami terima dan terkonfirmasi. Berikut adalah detail pendaftaran Anda:</p>
                        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Nama</td>
                                <td style="padding: 10px; border-bottom: 1px solid #eee;">{name}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #eee; font-weight: bold;">Event</td>
                                <td style="padding: 10px; border-bottom: 1px solid #eee;">{event_name}</td>
                            </tr>
                        </table>
                        
                        <div style="margin: 30px 0; text-align: center;">
                            <a href="{link_ticket}" style="background-color: #1a1235; color: #ffffff; padding: 15px 25px; text-decoration: none; border-radius: 10px; font-weight: bold;">LIHAT TIKET & QR CODE</a>
                        </div>
                        
                        <p>Silakan simpan email ini atau klik tombol di atas untuk mengakses tiket digital dan QR Code Anda saat berada di lokasi acara.</p>
                        <p>Sampai jumpa!</p>
                        <br>
                        <p>Salam hangat,<br><strong>Team Panitia {app_name}</strong></p>
                    </div>
                ',
                'whatsapp_content' => "Halo {name}!

Terima kasih telah mendaftar untuk acara *{event_name}*.

Pendaftaran Anda telah kami terima dan terkonfirmasi. Silakan klik link di bawah ini untuk melihat Tiket Digital dan QR Code Anda:

*Link Tiket:* {link_ticket}

Silakan simpan pesan ini dan tunjukkan QR Code tersebut kepada panitia saat berada di lokasi acara untuk proses Check-in.

Sampai jumpa di lokasi!

Salam hangat,
*Team Panitia {app_name}*",
                'category' => 'transactional',
            ]
        );

        // 2. Hubungkan otomatis ke semua event yang belum memiliki template konfirmasi
        $events = Event::whereNull('confirmation_template_id')->get();
        foreach ($events as $event) {
            $event->update([
                'confirmation_template_id' => $template->id
            ]);
        }
    }
}

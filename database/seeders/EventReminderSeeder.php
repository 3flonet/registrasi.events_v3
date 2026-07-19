<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventEmailTemplate;

class EventReminderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventEmailTemplate::updateOrCreate(
            ['subject' => 'Reminder: {event_name} is Happening Soon!'],
            [
                'category' => 'broadcast',
                'content' => '
                    <div style="font-family: sans-serif; line-height: 1.6; color: #1a1235; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; max-width: 600px; margin: 0 auto;">
                        <div style="background-color: #1a1235; padding: 40px; text-align: center; color: #ffffff;">
                            <h1 style="margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 0.1em;">Event Reminder</h1>
                        </div>
                        <div style="padding: 40px; background-color: #ffffff;">
                            <h2 style="color: #1a1235; margin-top: 0;">Halo {name},</h2>
                            <p>Hanya tinggal sedikit waktu lagi menuju acara <strong>{event_name}</strong>!</p>
                            <p>Kami sangat menantikan kehadiran Anda. Kami ingin memastikan Anda memiliki semua informasi yang dibutuhkan sebelum tiba di lokasi.</p>
                            
                            <div style="background-color: #f8fafc; border-radius: 15px; padding: 25px; margin: 25px 0; border-left: 5px solid #4f46e5;">
                                <p style="margin: 0 0 10px 0; font-weight: bold; color: #1a1235;">Siapkan Tiket Digital Anda:</p>
                                <p style="margin: 0; font-size: 13px; color: #64748b;">Anda wajib menunjukkan QR Code yang ada pada tiket untuk proses masuk (check-in) yang lebih cepat.</p>
                            </div>

                            <div style="text-align: center; margin: 40px 0;">
                                <a href="{link_ticket}" style="background-color: #4f46e5; color: #ffffff; padding: 18px 35px; text-decoration: none; border-radius: 15px; font-weight: 800; font-size: 12px; letter-spacing: 0.1em; display: inline-block; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);">AKSES TIKET & QR CODE</a>
                            </div>

                            <p>Sampai jumpa di lokasi!</p>
                            
                            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                                <p style="margin: 0; color: #94a3b8; font-size: 12px;">Salam hangat,</p>
                                <p style="margin: 5px 0 0 0; color: #1a1235; font-weight: bold;">Team Panitia {app_name}</p>
                            </div>
                        </div>
                    </div>
                ',
                'whatsapp_content' => "Halo {name}! 👋

Kami ingatkan kembali kehadiran Anda di acara *{event_name}* yang akan segera berlangsung.

Agar proses masuk (check-in) di lokasi lebih lancar dan cepat, mohon siapkan Tiket Digital Anda di link berikut:

*Akses Tiket & QR Code:* {link_ticket}

Cukup tunjukkan QR Code pada link tersebut kepada petugas gate saat Anda tiba.

Sampai jumpa di lokasi!

Salam hangat,
*Team Panitia {app_name}*"
            ]
        );
    }
}

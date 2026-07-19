<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventEmailTemplate;

class AutoCheckinTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventEmailTemplate::updateOrCreate(
            [
                'category' => 'auto_checkin',
                'event_id' => null, // Jadikan Global agar otomatis aktif di semua event
            ],
            [
                'subject' => '🎉 Selamat Datang di {event_name}!',
                'content' => '
                    <div style="font-family: sans-serif; line-height: 1.6; color: #1a1235; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden; max-width: 600px; margin: 0 auto;">
                        <div style="background-color: #1a1235; padding: 40px; text-align: center; color: #ffffff;">
                            <div style="font-size: 40px; margin-bottom: 10px;">🎊</div>
                            <h1 style="margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 0.1em;">Check-in Berhasil</h1>
                        </div>
                        <div style="padding: 40px; background-color: #ffffff; text-align: center;">
                            <h2 style="color: #1a1235; margin-top: 0;">Halo {name},</h2>
                            <p style="font-size: 16px; color: #475569;">Kehadiran Anda di acara <strong>{event_name}</strong> telah berhasil divalidasi oleh sistem kami.</p>
                            
                            <div style="background-color: #f0fdf4; border-radius: 15px; padding: 25px; margin: 25px 0; border: 1px solid #bbf7d0;">
                                <p style="margin: 0; font-weight: bold; color: #166534; font-size: 14px;">Selamat mengikuti rangkaian acara!</p>
                                <p style="margin: 5px 0 0 0; font-size: 12px; color: #15803d;">Semoga Anda mendapatkan pengalaman yang berkesan hari ini.</p>
                            </div>

                            <p style="font-size: 13px; color: #94a3b8; margin-top: 40px;">Salam hangat,<br>
                            <span style="color: #1a1235; font-weight: bold;">Team Panitia {app_name}</span></p>
                        </div>
                    </div>
                ',
                'whatsapp_content' => "🎊 *Check-in Berhasil!*

Halo *{name}*, Selamat Datang di *{event_name}*! 👋

Kehadiran Anda telah berhasil dikonfirmasi oleh sistem. Selamat mengikuti rangkaian acara dan semoga mendapatkan pengalaman yang berkesan hari ini.

Salam hangat,
*Team Panitia {app_name}*"
            ]
        );
    }
}

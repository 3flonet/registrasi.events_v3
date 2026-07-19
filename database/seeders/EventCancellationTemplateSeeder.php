<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventEmailTemplate;

class EventCancellationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subject = 'Pemberitahuan Pembatalan Acara: [EVENT_NAME]';
        
        $emailContent = '
        <div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #f0f0f0; border-radius: 20px; overflow: hidden; background-color: #ffffff;">
            <div style="background-color: #e11d48; padding: 40px 20px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px;">Pemberitahuan Penting</h1>
            </div>
            <div style="padding: 40px 30px; color: #1a1235; line-height: 1.6;">
                <p style="font-size: 16px; font-weight: bold;">Halo [PARTICIPANT_NAME],</p>
                <p>Kami memohon maaf yang sebesar-besarnya untuk menginformasikan bahwa acara:</p>
                <div style="background-color: #f8fafc; padding: 20px; border-left: 4px solid #e11d48; margin: 25px 0; border-radius: 8px;">
                    <strong style="display: block; font-size: 18px; color: #e11d48;">[EVENT_NAME]</strong>
                    <span style="font-size: 13px; color: #64748b; font-weight: bold; text-transform: uppercase;">Telah Dibatalkan</span>
                </div>
                <p>Keputusan ini diambil karena adanya kendala teknis yang tidak terduga. Kami memahami bahwa hal ini mungkin mengecewakan, dan kami tulus memohon maaf atas ketidaknyamanan yang ditimbulkan.</p>
                
                <h3 style="margin-top: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Langkah Selanjutnya:</h3>
                <ul style="padding-left: 20px;">
                    <li>Jika acara ini berbayar, tim admin kami akan segera memproses pengembalian dana (Refund) Anda dalam waktu 3-7 hari kerja.</li>
                    <li>Sertifikat atau materi yang mungkin sudah Anda akses sebelumnya akan tetap tersimpan di profil Anda jika tersedia.</li>
                </ul>
                
                <p style="margin-top: 40px;">Terima kasih atas pengertian dan dukungan Anda.</p>
                <p style="font-weight: bold; margin-bottom: 0;">Salam hangat,</p>
                <p style="margin-top: 5px;">Tim Management Event</p>
            </div>
            <div style="background-color: #f8fafc; padding: 20px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #f1f5f9;">
                &copy; ' . date('Y') . ' Registrasi.Events. Hak Cipta Dilindungi.
            </div>
        </div>';

        $whatsappContent = "*Pemberitahuan Penting: Pembatalan Acara*\n\n" .
            "Halo *[PARTICIPANT_NAME]*,\n\n" .
            "Kami memohon maaf menginformasikan bahwa acara:\n" .
            "*[EVENT_NAME]*\n" .
            "*RESMI DIBATALKAN.*\n\n" .
            "Keputusan ini diambil karena adanya kendala teknis yang tidak terduga. Kami memohon maaf atas ketidaknyamanan ini.\n\n" .
            "Untuk informasi lebih lanjut mengenai pengembalian dana (jika berbayar) atau info lainnya, silakan hubungi tim kami.\n\n" .
            "Terima kasih atas pengertian Anda.\n\n" .
            "-- Tim Management Event --";

        EventEmailTemplate::updateOrCreate(
            ['subject' => $subject, 'category' => 'event_cancellation'],
            [
                'content' => $emailContent,
                'whatsapp_content' => $whatsappContent,
                'event_id' => null
            ]
        );
    }
}

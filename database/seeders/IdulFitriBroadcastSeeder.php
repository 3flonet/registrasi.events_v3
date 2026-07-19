<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventEmailTemplate;

class IdulFitriBroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventEmailTemplate::updateOrCreate(
            ['subject' => 'Selamat Idul Fitri 1447 H - {app_name}'],
            [
                'content' => '
                    <div style="font-family: sans-serif; line-height: 1.6; color: #1a1235; max-width: 600px; margin: 0 auto; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden;">
                        <div style="background-color: #1a1235; padding: 40px; text-align: center; color: #ffffff;">
                             <h1 style="margin: 0; font-size: 24px; text-transform: uppercase; tracking: 0.1em;">Eid Mubarak</h1>
                        </div>
                        
                        <div style="padding: 40px; background-color: #ffffff;">
                            <h2 style="color: #1a1235; margin-top: 0;">Assalamu\'alaikum {name},</h2>
                            
                            <p>Segenap tim <strong>{app_name}</strong> mengucapkan:</p>
                            
                            <div style="margin: 30px 0; padding: 20px; background-color: #f8fafc; border-radius: 15px; border-left: 5px solid #6366f1;">
                                <h3 style="margin: 0; color: #4338ca;">Selamat Hari Raya Idul Fitri 1447 H</h3>
                                <p style="margin: 10px 0 0 0; font-style: italic; color: #64748b;">"Taqabbalallahu minna wa minkum, shiyamana wa shiyamakum."</p>
                            </div>
                            
                            <p>Semoga amal ibadah kita selama bulan suci Ramadhan diterima oleh Allah SWT, dan kita dipertemukan kembali dengan Ramadhan berikutnya dalam keadaan sehat dan penuh keberkahan.</p>
                            
                            <p>Mohon maaf lahir dan batin atas segala kekhilafan dalam pelayanan maupun interaksi kami selama ini.</p>
                            
                            <p style="margin-top: 40px;">Salam hangat,</p>
                            <p><strong>Team Panitia {app_name}</strong></p>
                        </div>
                        
                        <div style="background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">
                            Sent with heart from {app_name} Communications center
                        </div>
                    </div>
                ',
                'whatsapp_content' => "Eid Mubarak
Assalamu'alaikum {name},

Segenap tim *{app_name}* mengucapkan:
*Selamat Hari Raya Idul Fitri 1447 H*
\"Taqabbalallahu minna wa minkum, shiyamana wa shiyamakum.\"

Semoga amal ibadah kita selama bulan suci Ramadhan diterima oleh Allah SWT, dan kita dipertemukan kembali dengan Ramadhan berikutnya dalam keadaan sehat dan penuh keberkahan.

Mohon maaf lahir dan batin atas segala kekhilafan kami.

Salam hangat,
*Management {app_name}*"
            ]
        );
    }
}

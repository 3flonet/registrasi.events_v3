<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventInvoiceTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\EventEmailTemplate::updateOrCreate(
            ['subject' => 'Tagihan Pendaftaran - {event_name}'],
            [
                'category' => 'event_invoice',
                'content' => '
                    <p>Halo <strong>{name}</strong>,</p>
                    <p>Terima kasih telah mendaftar di event <strong>{event_name}</strong>. Pesanan Anda dengan kategori tiket <strong>{ticket_tier}</strong> telah kami terima.</p>
                    <p>Mohon segera lakukan pembayaran sebesar <strong>{total_bayar}</strong> untuk mengamankan slot kehadiran Anda melalui link di bawah ini:</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{link_invoice}" style="background-color: #4338ca; color: white; padding: 15px 25px; text-decoration: none; border-radius: 10px; font-weight: bold;">BAYAR SEKARANG</a>
                    </div>
                    <p>Jika tomboh tidak berfungsi, silakan copy-paste link berikut: {link_invoice}</p>
                    <p>Sampai jumpa di lokasi acara!</p>
                ',
                'whatsapp_content' => "📋 *Tagihan Pendaftaran - {event_name}*\n\nHalo *{name}*, pendaftaran Anda telah kami terima.\n\nSegera lakukan pembayaran sebesar *{total_bayar}* ({ticket_tier}) untuk mengamankan kuota Anda melalui link invoice berikut:\n\n🔗 {link_invoice}\n\n_Segera selesaikan pembayaran sebelum kuota habis. Terima kasih!_",
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\WhatsAppTemplate;
use Illuminate\Database\Seeder;

class WhatsAppTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama terlebih dahulu
        \Illuminate\Support\Facades\DB::table('event_email_templates')->update(['whatsapp_template_id' => null]);
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        WhatsAppTemplate::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $templates = [
            [
                'name' => 'konfirmasi_pendaftaran_event_v1',
                'category' => 'transactional',
                'meta_category' => 'MARKETING',
                'language_code' => 'id',
                'body_preview' => "Halo *{{1}}*! 👋\n\nPendaftaran Anda untuk event *{{2}}* telah dikonfirmasi dengan kode tiket *{{3}}*.\n\n📍 Lokasi acara di {{4}}. Harap datang tepat waktu, sampai jumpa di lokasi! 🚀",
                'parameters' => [
                    'header' => [
                        'type' => 'document',
                        'value' => 'ticket_pdf'
                    ],
                    'body' => [
                        'name',
                        'event_name',
                        'ticket_code',
                        'event_instruction'
                    ],
                    'buttons' => [
                        [
                            'index' => 0,
                            'type' => 'url',
                            'value' => 'ticket_url'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'checkin_otomatis_v1',
                'category' => 'auto_checkin',
                'meta_category' => 'UTILITY',
                'language_code' => 'id',
                'body_preview' => "Selamat Datang *{{1}}*! 👋\n\nKehadiran Anda di event *{{2}}* berhasil dicatat pada pukul *{{3}}*.\n\nNikmati acaranya! ✨",
                'parameters' => [
                    'header' => null,
                    'body' => [
                        'name',
                        'event_name',
                        'time'
                    ],
                    'buttons' => []
                ]
            ],
            [
                'name' => 'tagihan_pembayaran_v1',
                'category' => 'event_invoice',
                'meta_category' => 'UTILITY',
                'language_code' => 'id',
                'body_preview' => "Halo *{{1}}*!\n\nHarap selesaikan pembayaran pendaftaran event *{{2}}* Anda.\n\nTotal Tagihan: *{{3}}*",
                'parameters' => [
                    'header' => null,
                    'body' => [
                        'name',
                        'event_name',
                        'total_bayar'
                    ],
                    'buttons' => [
                        [
                            'index' => 0,
                            'type' => 'url',
                            'value' => 'payment_link'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'pengingat_acara_v1',
                'category' => 'reminder',
                'meta_category' => 'UTILITY',
                'language_code' => 'id',
                'body_preview' => "Halo *{{1}}*! 👋\n\nJangan lupa event *{{2}}* akan segera dimulai pada {{3}} pukul {{4}}.\n\n📍 Lokasi acara di {{5}}. Harap datang tepat waktu, sampai jumpa di lokasi! 🚀",
                'parameters' => [
                    'header' => null,
                    'body' => [
                        'name',
                        'event_name',
                        'date',
                        'time',
                        'event_instruction'
                    ],
                    'buttons' => [
                        [
                            'index' => 0,
                            'type' => 'url',
                            'value' => 'ticket_url'
                        ]
                    ]
                ]
            ]
        ];

        foreach ($templates as $tpl) {
            $tpl['meta_status'] = 'DRAFT';
            WhatsAppTemplate::updateOrCreate(
                ['name' => $tpl['name']],
                $tpl
            );
        }
    }
}

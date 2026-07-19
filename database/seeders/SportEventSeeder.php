<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketTier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SportEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $event = Event::updateOrCreate(
            ['slug' => 'green-run-2026'],
            [
                'name' => [
                    'en' => 'Green Run 2026 - Sustainable Marathon',
                    'id' => 'Green Run 2026 - Maraton Berkelanjutan'
                ],
                'theme' => [
                    'en' => 'Sustainable Steps for a Greener Future',
                    'id' => 'Langkah Berkelanjutan untuk Masa Depan yang Lebih Hijau'
                ],
                'description' => [
                    'en' => 'Green Run 2026 is a premium marathon event focused on environmental sustainability. Run for the planet!',
                    'id' => 'Green Run 2026 adalah acara maraton premium yang berfokus pada keberlanjutan lingkungan. Berlari untuk planet kita!'
                ],
                'type' => 'offline',
                'venue' => [
                    'en' => 'Bung Karno Stadium',
                    'id' => 'Stadion Utama Gelora Bung Karno'
                ],
                'daily_schedules' => [
                    [
                        'date' => '2026-06-21',
                        'agenda' => [
                            [
                                'start_time' => '05:00',
                                'end_time' => '05:30',
                                'title' => ['en' => 'Race Pack Collection & Warm Up', 'id' => 'Pengambilan Race Pack & Pemanasan'],
                                'description' => ['en' => 'Final prep and collective warm up session.', 'id' => 'Persiapan akhir dan sesi pemanasan bersama.']
                            ],
                            [
                                'start_time' => '06:00',
                                'end_time' => '10:00',
                                'title' => ['en' => 'Flag Off & Marathon Race', 'id' => 'Flag Off & Lomba Maraton'],
                                'description' => ['en' => 'The main race event starts here.', 'id' => 'Acara lari utama dimulai.']
                            ],
                            [
                                'start_time' => '10:30',
                                'end_time' => '12:00',
                                'title' => ['en' => 'Awarding Ceremony & Closing', 'id' => 'Upacara Penyerahan Penghargaan & Penutupan'],
                                'description' => ['en' => 'Winner announcements and closing festivities.', 'id' => 'Pengumuman pemenang dan perayaan penutupan.']
                            ]
                        ]
                    ]
                ],
                'start_date' => Carbon::parse('2026-06-21 05:00:00'),
                'end_date' => Carbon::parse('2026-06-21 11:00:00'),
                'quota' => 2500,
                'is_active' => true,
                'status' => 'upcoming',
                'visibility' => 'public',
                'is_paid_event' => true,
                'field_config' => [
                    ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true],
                    ['name' => 'phone', 'label' => 'WhatsApp Number', 'type' => 'text', 'required' => true],
                    ['name' => 'bib_name', 'label' => 'Name on BIB', 'type' => 'text', 'required' => true],
                    ['name' => 'tshirt_size', 'label' => 'T-Shirt Size', 'type' => 'select', 'options' => ['XS','S', 'M', 'L', 'XL', 'XXL'], 'required' => true],
                    ['name' => 'blood_type', 'label' => 'Blood Type', 'type' => 'select', 'options' => ['A', 'B', 'AB', 'O'], 'required' => false],
                    ['name' => 'emergency_contact', 'label' => 'Emergency Contact Name', 'type' => 'text', 'required' => true],
                    ['name' => 'emergency_phone', 'label' => 'Emergency Contact Phone', 'type' => 'text', 'required' => true],
                ],
            ]
        );

        // Create Ticket Tiers
        $tiers = [
            [
                'name' => '5K Fun Run',
                'description' => 'Perfect for beginners and families.',
                'price' => 200000,
                'quota' => 1000,
            ],
            [
                'name' => '10K Green Run',
                'description' => 'For active runners who want a challenge.',
                'price' => 350000,
                'quota' => 1000,
            ],
            [
                'name' => '21K Half Marathon',
                'description' => 'Professional level distance with complete facilities.',
                'price' => 550000,
                'quota' => 500,
            ],
        ];

        foreach ($tiers as $tier) {
            TicketTier::updateOrCreate(
                ['event_id' => $event->id, 'name' => $tier['name']],
                array_merge($tier, [
                    'max_per_user' => 1,
                    'sales_start_at' => Carbon::now(),
                    'sales_end_at' => Carbon::parse('2026-06-14 23:59:59'),
                    'is_active' => true,
                ])
            );
        }
    }
}

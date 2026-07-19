<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Trial / Masa Percobaan',
                'slug' => 'trial',
                'price' => 0,
                'duration_days' => 7,
                'event_limit' => 1,
                'registrant_limit' => 100,
                'user_limit' => 2,
                'description' => 'Paket percobaan otomatis untuk pendaftar baru agar bisa mencoba fitur dashboard.',
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'name' => 'Free Starter',
                'slug' => 'starter',
                'price' => 0,
                'duration_days' => 30,
                'event_limit' => 1,
                'registrant_limit' => 100,
                'user_limit' => 2,
                'description' => 'Cocok untuk komunitas kecil dan uji coba fitur dasar.',
                'is_active' => true,
                'is_popular' => false,
            ],
            [
                'name' => 'Pro Planner',
                'slug' => 'pro',
                'price' => 499000,
                'duration_days' => 30,
                'event_limit' => 10,
                'registrant_limit' => 5000,
                'user_limit' => 10,
                'description' => 'Solusi terbaik untuk wedding planner & event organizer profesional.',
                'is_active' => true,
                'is_popular' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 1999000,
                'duration_days' => 365,
                'event_limit' => -1, // Unlimited
                'registrant_limit' => -1, // Unlimited
                'user_limit' => -1, // Unlimited
                'description' => 'Kapasitas tanpa batas untuk korporat dan konser skala besar.',
                'is_active' => true,
                'is_popular' => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}

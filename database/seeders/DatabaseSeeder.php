<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(SubscriptionPlanSeeder::class);
        $this->call(SectionTemplateSeeder::class);
        $this->call(HeroSportyTemplateSeeder::class);
        $this->call(ThreeCardWithIconSeeder::class);
        $this->call(PricingRaceSportySeeder::class);
        $this->call(VenueDetailsSportySeeder::class);
        $this->call(CtaRegistrationSportySeeder::class);
        $this->call(CtaSponsorshipSportySeeder::class);
        $this->call(ChooseRoleSportySeeder::class);
        $this->call(SportEventSeeder::class);
        $this->call(WelcomeSectionSeeder::class);
        $this->call(SocialWallMenuItemSeeder::class);
        $this->call(IdulFitriBroadcastSeeder::class);
        // $this->call(DefaultConfirmationSeeder::class);
        // $this->call(AutoCheckinTemplateSeeder::class);
        // $this->call(EventCancellationTemplateSeeder::class);
        // $this->call(EventInvoiceTemplateSeeder::class);
        $this->call(HeroBentoSeeder::class);
        $this->call(MosaicGridSeeder::class);
        $this->call(MessageTemplateCategorySeeder::class);
        $this->call(LegalPagesAndMenusSeeder::class);
        $this->call(PaymentChannelConfigSeeder::class);


        // CREATE SUPER ADMIN USER
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@registrasi.events'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('Super Admin');
    }
}

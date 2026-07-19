<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LegalPagesAndMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Legal Pages Data
        $pages = [
            [
                'slug' => 'privacy-policy',
                'title' => [
                    'en' => 'Privacy Policy',
                    'id' => 'Kebijakan Privasi',
                ],
                'content' => [
                    'en' => '<h2>Privacy Policy</h2><p>Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal information in accordance with Indonesian laws, including the Personal Data Protection (PDP) Law. By using our services, you consent to the data practices described in this policy.</p><h3>1. Data Collection</h3><p>We collect information you provide directly to us when registering for events, such as your name, email address, and contact details.</p><h3>2. Use of Information</h3><p>Your data is used to process event registrations, communicate updates, and improve our services.</p><h3>3. Data Security</h3><p>We implement technical and organizational measures to safeguard your personal data against unauthorized access or disclosure.</p>',
                    'id' => '<h2>Kebijakan Privasi</h2><p>Privasi Anda sangat penting bagi kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda sesuai dengan hukum di Indonesia, termasuk Undang-Undang Pelindungan Data Pribadi (UU PDP). Dengan menggunakan layanan kami, Anda menyetujui praktik data yang dijelaskan dalam kebijakan ini.</p><h3>1. Pengumpulan Data</h3><p>Kami mengumpulkan informasi yang Anda berikan langsung kepada kami saat mendaftar acara, seperti nama, alamat email, dan detail kontak Anda.</p><h3>2. Penggunaan Informasi</h3><p>Data Anda digunakan untuk memproses pendaftaran acara, menyampaikan pembaruan, dan meningkatkan layanan kami.</p><h3>3. Keamanan Data</h3><p>Kami menerapkan langkah-langkah teknis dan organisasional untuk melindungi data pribadi Anda dari akses atau pengungkapan yang tidak sah.</p>',
                ],
                'status' => 'published',
            ],
            [
                'slug' => 'terms-of-service',
                'title' => [
                    'en' => 'Terms of Service',
                    'id' => 'Syarat dan Ketentuan',
                ],
                'content' => [
                    'en' => '<h2>Terms of Service</h2><p>Welcome to our platform. These Terms of Service govern your use of our website and services in accordance with the Electronic Information and Transactions (ITE) Law of Indonesia. By accessing our site, you agree to comply with these terms.</p><h3>1. User Responsibilities</h3><p>Users must provide accurate information during registration and are responsible for maintaining the confidentiality of their accounts.</p><h3>2. Event Registration</h3><p>All registrations are subject to availability and the specific rules set by event organizers.</p><h3>3. Limitation of Liability</h3><p>We are not liable for any indirect or consequential damages arising from the use of our platform.</p>',
                    'id' => '<h2>Syarat dan Ketentuan</h2><p>Selamat datang di platform kami. Syarat dan Ketentuan ini mengatur penggunaan situs web dan layanan kami sesuai dengan Undang-Undang Informasi dan Transaksi Elektronik (UU ITE) Indonesia. Dengan mengakses situs kami, Anda setuju untuk mematuhi ketentuan ini.</p><h3>1. Tanggung Jawab Pengguna</h3><p>Pengguna harus memberikan informasi yang akurat saat pendaftaran dan bertanggung jawab untuk menjaga kerahasiaan akun mereka.</p><h3>2. Pendaftaran Acara</h3><p>Semua pendaftaran bergantung pada ketersediaan dan aturan khusus yang ditetapkan oleh penyelenggara acara.</p><h3>3. Batasan Tanggung Jawab</h3><p>Kami tidak bertanggung jawab atas kerugian tidak langsung atau konsekuensial yang timbul dari penggunaan platform kami.</p>',
                ],
                'status' => 'published',
            ],
        ];

        foreach ($pages as $pageData) {
            // Create or Update Page
            $page = Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                [
                    'title' => $pageData['title'],
                    'content' => $pageData['content'],
                    'status' => $pageData['status'],
                ]
            );

            // 2. Create or Update Menu Item for this page
            MenuItem::updateOrCreate(
                ['link' => '/pages/' . $pageData['slug']],
                [
                    'label' => $pageData['title'],
                    'location' => 'footer_legal',
                    'target' => '_self',
                    'order' => ($pageData['slug'] === 'privacy-policy' ? 1 : 2),
                ]
            );
        }
    }
}

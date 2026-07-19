# 🌌 Registrasi.Events v3 — The Future of Event Management

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

### 🔌 API Integrations & Connectivity
![Midtrans](https://img.shields.io/badge/Midtrans-003580?style=for-the-badge&logo=icloud&logoColor=white)
![Meta WhatsApp Cloud API](https://img.shields.io/badge/Meta_WhatsApp_Cloud_API-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)
![Fonnte WA](https://img.shields.io/badge/Fonnte_WA-25D366?style=for-the-badge&logo=whatsapp&logoColor=gray)
![Google Drive](https://img.shields.io/badge/Google_Drive-4285F4?style=for-the-badge&logo=googledrive&logoColor=white)
![AWS S3](https://img.shields.io/badge/AWS_S3-569A31?style=for-the-badge&logo=amazons3&logoColor=white)
![Pusher](https://img.shields.io/badge/Pusher-300D4F?style=for-the-badge&logo=pusher&logoColor=white)

> **Architecting unforgettable attendee experiences through high-performance tech stacks.**

Welcome to **Registrasi.Events v3**, a next-generation event management ecosystem built for scale, speed, multi-tenant operations, and stunning aesthetics. This platform bridges the gap between complex enterprise requirements and seamless user interactions.

---

## 🚀 Key Features

### 🏢 Multi-Tenant / Multi-Organizer System
Run multiple organizers on a single platform instance:
- **Tenant Isolation**: Separate event management, media files, and settings for each organizer.
- **Dynamic PWA Generation**: Automatic branding, metadata, and custom favicon generation according to the active tenant's settings.

### 🛠️ Advanced Page Builder
Build stunning landing pages with our **Modular Dynamic Sections**:
- **Dynamic Blade Rendering**: Inject logic directly from the database dynamically.
- **Section Templates**: Blueprints for reusable UI components (Hero Bento, Grid Mosaic, etc.).
- **Translatable Content**: Native multi-language support (ID/EN) using JSON structures.

### 🎫 Intelligent Ticketing & Check-in
- **Omni-Channel Entry**: Support for RFID, QR codes, and manual gate management.
- **WhatsApp Automation**: Real-time ticket delivery & check-in alerts powered by **Meta WhatsApp Cloud API** (with legacy fallback support for Fonnte API).
- **Automated Invoicing & Certificates**: Instant PDF generation and email dispatch upon registration, along with public certificate verification.

### 📊 Admin Ecosystem
- **Modular Dashboard**: High-impact grid navigation for mobile-first administration.
- **Real-time Analytics**: Live tracking of registrations, revenue, and attendee demographics.
- **Role-Based Access**: Granular control via Spatie Permissions.

---

## 🛠️ Tech Stack & Architecture

| Component | Technology | Role |
| :--- | :--- | :--- |
| **Backend** | Laravel 10 (PHP 8.2+) | Core Logic & API |
| **Frontend** | Livewire 3 / Alpine.js | Reactive Interfaces |
| **Styling** | Tailwind CSS | Utility-first Design |
| **Database** | MySQL / PostgreSQL | Structured Storage |
| **Dynamic UI** | `Blade::render()` | CMS Capabilities |
| **Media** | Spatie MediaLibrary | Asset Management |

---

## 📥 Installation

### 1. Requirements
Ensure your environment meets the minimum standards:
- **PHP** 8.2 or higher
- **Composer** 2.0+
- **Node.js** & **NPM** (latest LTS)
- **Database**: MySQL 8.0+ or PostgreSQL

### 2. Setup
```bash
# Clone the repository
git clone https://github.com/3flonet/registrasi.events_v3.git

# Enter directory
cd registrasi.events_v3

# Install PHP dependencies
composer install

# Install JS dependencies
npm install && npm run build

# Configure Environment
cp .env.example .env
php artisan key:generate

# Run Migrations & Seeders
php artisan migrate --seed

# Create symlink for public storage access
php artisan storage:link
```

---

## ⚡ Deployment & Workflow

This project follows a strict architectural pattern to ensure maintainability:

1. **Section Seeders**: Use dedicated seeders (e.g., `HeroBentoSeeder`, `MosaicGridSeeder`) to register UI blueprints.
2. **Handle Notifications**: Logic is centralized in traits (e.g., `HandlesCheckin.php`) for reusable messaging.
3. **Admin UI**: Built with responsive modular containers for maximum usability across devices.

---

## 🚀 Deployment to cPanel

### 1. Persiapan File
1.  **Optimasi Dependensi (PHP)**: Di terminal lokal, jalankan perintah untuk membersihkan package development dan mengoptimasi class map:
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
2.  **Build Aset (JS/CSS)**: Jalankan `npm run build` untuk menghasilkan file produksi di folder `public/build`.
3.  **Kompresi**: Kompres seluruh folder proyek menjadi satu file `.zip`.
    *   **Wajib Masuk**: folder `app`, `bootstrap`, `config`, `database`, `lang`, `public`, `resources`, `routes`, `vendor`, file `.env`, `artisan`, `composer.json`.
    *   **Boleh Dibuang**: folder `node_modules`, `tests`, `.git`, `.github`.

### 2. Upload & Extract
1.  Login ke cPanel File Manager.
2.  Upload file `.zip` ke folder **root** akun Anda (satu level di atas `public_html`). Contoh: `/home/username/registrasi_events`.
3.  Extract file `.zip` tersebut.

### 3. Konfigurasi Public Folder (Web Root)
Ada dua cara umum untuk menghubungkan Laravel ke web root cPanel:
*   **Opsi A (Symlink - Direkomendasikan):**
    Pastikan folder `public_html` kosong atau dihapus (backup jika perlu), lalu buat symlink melalui Terminal cPanel:
    ```bash
    ln -s /home/username/registrasi_events/public /home/username/public_html
    ```
*   **Opsi B (Edit index.php):**
    Pindahkan semua isi folder `registrasi_events/public/*` ke dalam `public_html`. Kemudian edit `public_html/index.php` dan sesuaikan path pada baris berikut:
    ```php
    require __DIR__.'/../registrasi_events/vendor/autoload.php';
    $app = require_once __DIR__.'/../registrasi_events/bootstrap/app.php';
    ```

### 4. Database & Environment
1.  Buat Database MySQL, User, dan Password melalui menu **MySQL Databases** di cPanel.
2.  Edit file `.env` di folder proyek (`/home/username/registrasi_events/.env`):
    *   `APP_ENV=production`
    *   `APP_DEBUG=false`
    *   `APP_URL=https://domain-anda.com`
    *   Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.

### 5. Finalisasi via Terminal cPanel
Buka menu **Terminal** di cPanel dan jalankan perintah optimasi:
```bash
cd registrasi_events
php artisan migrate --force --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Cron Job (Laravel Scheduler)
Penting agar fitur otomatisasi (pembatalan tiket expired, WA broadcast) berjalan. Tambahkan Cron Job baru di cPanel dengan frekuensi **Every Minute (* * * * *)**:
```bash
/usr/local/bin/php /home/username/registrasi_events/artisan schedule:run >> /dev/null 2>&1
```

---

## 🛸 The Vision

Registrasi.Events v3 is more than a registration tool; it's a **growth engine** for event organizers. By leveraging cutting-edge web technologies, we ensure that every interaction—from the first visit to the final check-out—is fast, secure, and visually breathtaking.

---

## 👨‍💻 Developed By
- **3flo** — [github.com/3flonet](https://github.com/3flonet)
- **Team** — [github.com/bennysueb](https://github.com/bennysueb)

---

<p align="center">
  <b>Digital Excellence. Powered by ❤️ 3flo.</b>
</p>

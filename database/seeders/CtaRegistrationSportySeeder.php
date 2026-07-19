<?php

namespace Database\Seeders;

use App\Models\SectionTemplate;
use Illuminate\Database\Seeder;

class CtaRegistrationSportySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SectionTemplate::updateOrCreate(
            ['slug' => 'cta-registration-sporty'],
            [
                'name' => ['en' => 'CTA Registration Sporty', 'id' => 'CTA Pendaftaran Sporty'],
                'html_content' => '<section class="gr-cta-section">
    {{-- Decorative Background Effect --}}
    <div class="gr-cta-bg-overlay"></div>
    
    <div class="gr-cta-wrapper">
        <div class="gr-cta-content">
            <h2 class="gr-cta-title">{{ $main_title }}</h2>
            <p class="gr-cta-subtitle">{{ $subtitle }}</p>

            <div class="gr-cta-actions">
                <a href="{{ $btn1_url }}" class="gr-btn-primary-volt">
                    {{ $btn1_text }}
                </a>
                <a href="{{ $btn2_url }}" class="gr-btn-outline-white">
                    {{ $btn2_text }}
                </a>
            </div>

            <p class="gr-cta-footer">{{ $footer_note }}</p>
        </div>
    </div>
</section>',
                'css_content' => '@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Teko:wght@400;500;600;700&display=swap");

.gr-cta-section {
    position: relative; padding: 10rem 0;
    background-color: #064e3b; /* deep emerald */
    font-family: "Plus Jakarta Sans", sans-serif;
    color: white; text-align: center;
    overflow: hidden;
}

.gr-cta-bg-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.gr-cta-wrapper { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; position: relative; z-index: 10; }

.gr-cta-title {
    font-family: "Teko", sans-serif; font-size: 5rem; font-weight: 700;
    text-transform: uppercase; font-style: italic; line-height: 0.9;
    margin-bottom: 2rem;
}
@media (min-width: 768px) { .gr-cta-title { font-size: 6.5rem; } }

.gr-cta-subtitle {
    font-size: 1.125rem; font-weight: 500; color: rgba(255,255,255,0.8);
    max-width: 40rem; margin: 0 auto 4rem; line-height: 1.6;
}

.gr-cta-actions {
    display: flex; flex-wrap: wrap; justify-content: center; gap: 1.5rem; margin-bottom: 5rem;
}

.gr-btn-primary-volt {
    padding: 1.25rem 3.5rem; background-color: #a3e635; /* volt */
    color: #064e3b; font-weight: 800; font-size: 1.25rem;
    border-radius: 9999px; text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 20px 25px -5px rgba(163, 230, 53, 0.25);
}
.gr-btn-primary-volt:hover { transform: translateY(-5px); box-shadow: 0 30px 35px -5px rgba(163, 230, 53, 0.35); }

.gr-btn-outline-white {
    padding: 1.25rem 3.5rem; background-color: transparent;
    border: 2px solid rgba(255,255,255,0.2);
    color: white; font-weight: 700; font-size: 1.25rem;
    border-radius: 9999px; text-decoration: none;
    transition: all 0.3s;
}
.gr-btn-outline-white:hover { border-color: white; background-color: rgba(255,255,255,0.05); }

.gr-cta-footer {
    font-size: 0.75rem; color: rgba(255,255,255,0.4); font-weight: 500;
    text-transform: uppercase; letter-spacing: 0.1em;
}',
                'fields' => [
                    ['name' => 'main_title', 'type' => 'text', 'label' => 'Main Center Title'],
                    ['name' => 'subtitle', 'type' => 'textarea', 'label' => 'Subtitle text'],
                    ['name' => 'btn1_text', 'type' => 'text', 'label' => 'Primary Button Text'],
                    ['name' => 'btn1_url', 'type' => 'text', 'label' => 'Primary Button URL'],
                    ['name' => 'btn2_text', 'type' => 'text', 'label' => 'Secondary Button Text'],
                    ['name' => 'btn2_url', 'type' => 'text', 'label' => 'Secondary Button URL'],
                    ['name' => 'footer_note', 'type' => 'text', 'label' => 'Small Footer Note'],
                ],
            ]
        );
    }
}

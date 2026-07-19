<?php

namespace Database\Seeders;

use App\Models\SectionTemplate;
use Illuminate\Database\Seeder;

class CtaSponsorshipSportySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SectionTemplate::updateOrCreate(
            ['slug' => 'cta-sponsorship-sporty'],
            [
                'name' => ['en' => 'CTA Sponsorship Sporty', 'id' => 'CTA Sponsor Sporty'],
                'html_content' => '<section class="gr-sponsor-section">
    <div class="gr-sponsor-wrapper">
        <div class="gr-sponsor-grid">
            
            {{-- Left Column: Brand Inbound --}}
            <div class="gr-sponsor-main">
                <span class="gr-pre-title">{{ $pre_title }}</span>
                <h2 class="gr-main-title">
                    {{ $title_line_1 }} <br>
                    <span class="text-volt">{{ $title_line_2 }}</span>
                </h2>
                <div class="gr-main-desc">{!! $description !!}</div>

                <div class="gr-inquiry-box">
                    <p class="gr-inquiry-tag">{{ $inquiry_tag }}</p>
                    <div class="gr-inquiry-details">
                        <div class="gr-inquiry-item">
                            <span>Email</span>
                            <strong>{{ $email }}</strong>
                        </div>
                        <div class="gr-inquiry-item">
                            <span>WhatsApp</span>
                            <strong>{{ $whatsapp }}</strong>
                        </div>
                    </div>
                </div>

                <div class="gr-sponsor-actions">
                    <a href="{{ $btn1_url }}" class="gr-btn-dark">{{ $btn1_text }}</a>
                    <a href="{{ $btn2_url }}" class="gr-btn-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        {{ $btn2_text }}
                    </a>
                </div>
            </div>

            {{-- Right Column: Value Props --}}
            <div class="gr-sponsor-features">
                <div class="gr-feature-card group">
                    <div class="gr-feature-icon-box bg-rose-50 text-rose-500">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="gr-feature-content">
                        <h3>{{ $f1_title }}</h3>
                        <p>{{ $f1_desc }}</p>
                    </div>
                </div>

                <div class="gr-feature-card group">
                    <div class="gr-feature-icon-box bg-emerald-50 text-emerald-500">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="gr-feature-content">
                        <h3>{{ $f2_title }}</h3>
                        <p>{{ $f2_desc }}</p>
                    </div>
                </div>

                <div class="gr-feature-card group">
                    <div class="gr-feature-icon-box bg-amber-50 text-amber-500">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="gr-feature-content">
                        <h3>{{ $f3_title }}</h3>
                        <p>{{ $f3_desc }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>',
                'css_content' => '@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Teko:wght@400;500;600;700&display=swap");

.gr-sponsor-section {
    padding: 8rem 0; background-color: #f8fafc;
    font-family: "Plus Jakarta Sans", sans-serif;
    background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
    background-size: 20px 20px;
}

.gr-sponsor-wrapper { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }

.gr-sponsor-grid {
    display: grid; gap: 4rem; align-items: start;
}
@media (min-width: 1024px) { .gr-sponsor-grid { grid-template-columns: 1.2fr 0.8fr; } }

/* Main Brand Column */
.gr-pre-title {
    color: #1e293b; font-weight: 800; font-size: 0.75rem; letter-spacing: 0.1em; text-transform: uppercase;
}

.gr-main-title {
    font-family: "Teko", sans-serif; font-size: 5rem; font-weight: 700;
    line-height: 0.9; text-transform: uppercase; font-style: italic; margin: 1.5rem 0 2rem; color: #0f172a;
}
.text-volt { color: #a3e635; -webkit-text-stroke: 1px #064e3b; }

.gr-main-desc { color: #64748b; line-height: 1.7; font-size: 1.125rem; max-width: 36rem; margin-bottom: 3rem; }
.gr-main-desc strong { color: #0f172a; font-weight: 700; }

.gr-inquiry-box {
    background-color: #f1f5f9; padding: 2.5rem; border-radius: 0.5rem; margin-bottom: 3rem;
    border-left: 4px solid #cbd5e1;
}

.gr-inquiry-tag { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em; margin-bottom: 1.5rem; }

.gr-inquiry-details { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
.gr-inquiry-item span { display: block; font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.25rem; }
.gr-inquiry-item strong { display: block; font-size: 1.125rem; color: #0f172a; font-weight: 800; }

.gr-sponsor-actions { display: flex; flex-wrap: wrap; gap: 1.5rem; }

.gr-btn-dark {
    padding: 1.25rem 3rem; background-color: #0f172a; color: white;
    font-weight: 800; border-radius: 9999px; text-decoration: none;
    transition: all 0.3s;
}
.gr-btn-dark:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(15, 23, 42, 0.2); }

.gr-btn-white {
    padding: 1.25rem 3rem; background-color: white; border: 1px solid #e2e8f0;
    color: #0f172a; font-weight: 700; border-radius: 9999px; text-decoration: none;
    display: inline-flex; align-items: center; transition: all 0.3s;
}
.gr-btn-white:hover { border-color: #0f172a; background-color: #f8fafc; }

/* Features Column */
.gr-sponsor-features { display: grid; gap: 1.5rem; }

.gr-feature-card {
    background-color: white; border: 1px solid #f1f5f9; padding: 2rem;
    border-radius: 1.5rem; display: flex; gap: 1.5rem;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.gr-feature-card:hover { transform: scale(1.02); border-color: #e2e8f0; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }

.gr-feature-icon-box {
    width: 3.5rem; height: 3.5rem; border-radius: 1rem;
    display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;
}

.gr-feature-content h3 { font-size: 1.125rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; }
.gr-feature-content p { font-size: 0.875rem; color: #64748b; line-height: 1.5; }

.bg-rose-50 { background-color: #fff1f2; } .text-rose-500 { color: #f43f5e; }
.bg-emerald-50 { background-color: #ecfdf5; } .text-emerald-500 { color: #10b981; }
.bg-amber-50 { background-color: #fffbeb; } .text-amber-500 { color: #f59e0b; }',
                'fields' => [
                    ['name' => 'pre_title', 'type' => 'text', 'label' => 'Pre Title (e.g. CORPORATE PARTNERSHIP)'],
                    ['name' => 'title_line_1', 'type' => 'text', 'label' => 'Title Line 1 (e.g. MAXIMIZE YOUR)'],
                    ['name' => 'title_line_2', 'type' => 'text', 'label' => 'Title Line 2 (Highlight)'],
                    ['name' => 'description', 'type' => 'textarea', 'label' => 'Main Description (supports HTML)'],
                    ['name' => 'inquiry_tag', 'type' => 'text', 'label' => 'Inquiry Box Label'],
                    ['name' => 'email', 'type' => 'text', 'label' => 'Inquiry Email'],
                    ['name' => 'whatsapp', 'type' => 'text', 'label' => 'Inquiry WhatsApp'],
                    ['name' => 'btn1_text', 'type' => 'text', 'label' => 'Btn 1 Text'],
                    ['name' => 'btn1_url', 'type' => 'text', 'label' => 'Btn 1 URL'],
                    ['name' => 'btn2_text', 'type' => 'text', 'label' => 'Btn 2 Text'],
                    ['name' => 'btn2_url', 'type' => 'text', 'label' => 'Btn 2 URL'],
                    ['name' => 'f1_title', 'type' => 'text', 'label' => 'Feature 1 Title'],
                    ['name' => 'f1_desc', 'type' => 'textarea', 'label' => 'Feature 1 Desc'],
                    ['name' => 'f2_title', 'type' => 'text', 'label' => 'Feature 2 Title'],
                    ['name' => 'f2_desc', 'type' => 'textarea', 'label' => 'Feature 2 Desc'],
                    ['name' => 'f3_title', 'type' => 'text', 'label' => 'Feature 3 Title'],
                    ['name' => 'f3_desc', 'type' => 'textarea', 'label' => 'Feature 3 Desc'],
                ],
            ]
        );
    }
}

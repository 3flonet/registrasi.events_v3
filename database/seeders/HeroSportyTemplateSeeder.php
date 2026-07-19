<?php

namespace Database\Seeders;

use App\Models\SectionTemplate;
use Illuminate\Database\Seeder;

class HeroSportyTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SectionTemplate::updateOrCreate(
            ['slug' => 'hero-sporty-theme'],
            [
                'name' => ['en' => 'Hero Sporty Theme', 'id' => 'Hero Tema Sporty'],
                'html_content' => '<section class="gr-hero-section">
    {{-- Background Image --}}
    <div class="gr-hero-bg">
        <img src="{{ $bg_image_url }}" alt="Background" class="gr-bg-img">
        <div class="gr-bg-overlay-grad"></div>
        <div class="gr-bg-overlay-white"></div>
    </div>

    {{-- Decorative Blobs --}}
    <div class="gr-blob gr-blob-volt"></div>
    <div class="gr-blob gr-blob-emerald"></div>

    <div class="gr-hero-wrapper">
        <div class="gr-hero-grid">
            
            {{-- Left Column: Content --}}
            <div class="gr-hero-content">
                {{-- Tag --}}
                <div class="gr-hero-tag-box">
                    <span class="gr-tag-skew">{{ $tag_text }}</span>
                </div>
                
                {{-- Title --}}
                <h1 class="gr-hero-title">
                    {{ $title_line_1 }} <br>
                    <span class="gr-text-gradient">{{ $title_line_2 }}</span>
                </h1>
                
                <p class="gr-hero-desc">
                    "{{ $subtitle }}" <br>
                    <span class="gr-desc-small">{{ $description }}</span>
                </p>

                {{-- Date & Venue Info --}}
                <div class="gr-info-bar">
                    <div class="gr-info-item group">
                        <div class="gr-info-icon">🗓️</div>
                        <div>
                            <p class="gr-info-label">Date</p>
                            <p class="gr-info-val">{{ $date_text }}</p>
                        </div>
                    </div>
                    <div class="gr-info-item group">
                        <div class="gr-info-icon">📍</div>
                        <div>
                            <p class="gr-info-label">Venue</p>
                            <p class="gr-info-val">{{ $venue_text }}</p>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="gr-hero-actions">
                    <a href="{{ $btn1_url }}" class="gr-btn-primary">
                        {{ $btn1_text }}
                    </a>
                    <a href="{{ $btn2_url }}" class="gr-btn-glass">
                        {{ $btn2_text }}
                    </a>
                </div>
            </div>

            {{-- Right Column: Stats Card (Floating) --}}
            <div class="gr-hero-stats">
                <div class="gr-stats-card">
                    <div class="gr-stats-grid">
                        {{-- Stat 1 --}}
                        <div class="gr-stat-box">
                            <p class="gr-stat-num">{{ $stat1_val }}</p>
                            <p class="gr-stat-label">{{ $stat1_label }}</p>
                        </div>
                        {{-- Stat 2 --}}
                        <div class="gr-stat-box">
                            <p class="gr-stat-num">{{ $stat2_val }}</p>
                            <p class="gr-stat-label">{{ $stat2_label }}</p>
                        </div>
                        {{-- Stat 3 --}}
                        <div class="gr-stat-box">
                            <p class="gr-stat-num">{{ $stat3_val }}</p>
                            <p class="gr-stat-label">{{ $stat3_label }}</p>
                        </div>
                        {{-- Stat 4 --}}
                        <div class="gr-stat-box">
                            <p class="gr-stat-num">{{ $stat4_val }}</p>
                            <p class="gr-stat-label">{{ $stat4_label }}</p>
                        </div>
                    </div>
                </div>
                {{-- Decorative Blob behind card --}}
                <div class="gr-stats-blob"></div>
            </div>

        </div>
    </div>
</section>',
                'css_content' => '/* Import Fonts */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Teko:wght@400;500;600;700&display=swap");

.gr-hero-section {
    position: relative;
    min-height: 100vh;
    display: flex; align-items: center;
    overflow: hidden;
    font-family: "Plus Jakarta Sans", sans-serif;
    color: #1e293b; /* slate-800 */
    padding-top: 5rem;
}

/* Backgrounds */
.gr-hero-bg { position: absolute; inset: 0; z-index: 0; }
.gr-bg-img {
    width: 100%; height: 100%; object-fit: cover;
    opacity: 0.9; filter: blur(2px); mix-blend-mode: luminosity;
}
.gr-bg-overlay-grad {
    position: absolute; inset: 0;
    background: linear-gradient(to right, white, rgba(255,255,255,0.9), rgba(255,255,255,0.4));
}
.gr-bg-overlay-white {
    position: absolute; inset: 0;
    background: linear-gradient(to top, white, transparent, transparent);
}

/* Blobs */
.gr-blob {
    position: absolute; border-radius: 9999px; filter: blur(120px); z-index: 0; mix-blend-mode: multiply;
}
.gr-blob-volt {
    top: 25%; left: 25%; width: 24rem; height: 24rem;
    background-color: rgba(163, 230, 53, 0.4); /* volt/40 */
    animation: pulse 4s infinite;
}
.gr-blob-emerald {
    bottom: 0; right: 0; width: 600px; height: 600px;
    background-color: rgba(52, 211, 153, 0.2);
}
@keyframes pulse { 50% { opacity: .7; } }

/* Wrapper */
.gr-hero-wrapper {
    max-width: 80rem; margin: 0 auto; padding: 0 1.5rem;
    position: relative; z-index: 10; width: 100%;
}

.gr-hero-grid {
    display: grid; gap: 3rem; align-items: center;
}
@media (min-width: 1024px) { .gr-hero-grid { grid-template-columns: 1fr 1fr; } }

/* --- Left Column --- */
.gr-hero-tag-box {
    display: inline-flex; align-items: center;
    background-color: #064e3b; /* greenrun-dark */
    padding: 0.25rem 0.75rem; margin-bottom: 1.5rem;
    transform: skewX(-10deg);
    box-shadow: 0 10px 15px -3px rgba(6, 78, 59, 0.2);
}
.gr-tag-skew {
    display: block; transform: skewX(10deg);
    color: white; font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.1em;
}

.gr-hero-title {
    font-family: "Teko", sans-serif;
    font-size: 4.5rem; /* 7xl */
    font-weight: 700; line-height: 0.85; margin-bottom: 1.5rem;
    text-transform: uppercase; font-style: italic; color: #0f172a;
}
@media (min-width: 1024px) { .gr-hero-title { font-size: 8rem; /* 9xl */ } }

.gr-text-gradient {
    background: linear-gradient(to right, #10b981, #a3e635); /* primary to volt */
    -webkit-background-clip: text; color: transparent;
    padding-right: 2rem; padding-bottom: 1rem;
}

.gr-hero-desc {
    font-size: 1.25rem; /* xl */
    font-weight: 500; color: #475569; margin-bottom: 2rem; max-width: 32rem;
}
.gr-desc-small { font-size: 1rem; font-weight: 400; display: block; margin-top: 0.5rem; }

/* Info Bar */
.gr-info-bar { display: flex; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 3rem; }

.gr-info-item { display: flex; align-items: center; gap: 1rem; }

.gr-info-icon {
    width: 3rem; height: 3rem; background-color: white;
    border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}
.group:hover .gr-info-icon { transform: scale(1.1); }

.gr-info-label { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
.gr-info-val { font-weight: 700; color: #1e293b; }

/* Buttons */
.gr-hero-actions { display: flex; gap: 1rem; }

.gr-btn-primary {
    padding: 1rem 2.5rem; background-color: #10b981; /* primary */
    color: white; font-weight: 700; font-size: 1.125rem;
    border-radius: 9999px; text-decoration: none;
    box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.3);
    transition: all 0.3s;
}
.gr-btn-primary:hover { background-color: #059669; transform: translateY(-3px); }

.gr-btn-glass {
    padding: 1rem 2.5rem; background-color: rgba(255,255,255,0.8);
    backdrop-filter: blur(4px); border: 1px solid #e2e8f0;
    color: #334155; font-weight: 700; font-size: 1.125rem;
    border-radius: 9999px; text-decoration: none; transition: all 0.3s;
}
.gr-btn-glass:hover { border-color: #10b981; color: #10b981; }


/* --- Right Column (Stats Card) --- */
.gr-hero-stats { position: relative; display: none; }
@media (min-width: 1024px) { .gr-hero-stats { display: block; } }

.gr-stats-card {
    background-color: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(8px); padding: 2rem;
    border-radius: 1.5rem; border: 1px solid rgba(255,255,255,0.5);
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
    transform: rotate(3deg); transition: transform 0.5s;
    position: relative; z-index: 10;
}
.gr-stats-card:hover { transform: rotate(0deg); }

.gr-stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; text-align: center; }

.gr-stat-box {
    padding: 1rem; background-color: rgba(248, 250, 252, 0.8);
    border-radius: 1rem; border: 1px solid #f1f5f9;
}

.gr-stat-num {
    font-family: "Teko", sans-serif; font-size: 3rem; font-weight: 700;
    color: #064e3b; /* greenrun-dark */ line-height: 1;
}
.gr-stat-label {
    font-size: 0.75rem; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem;
}

.gr-stats-blob {
    position: absolute; bottom: -2.5rem; left: -2.5rem;
    width: 6rem; height: 6rem; background-color: #a3e635; /* volt */
    border-radius: 9999px; opacity: 0.5; filter: blur(24px); z-index: 0;
}',
                'fields' => [
                    ['name' => 'bg_image_url', 'type' => 'image', 'label' => 'Background Image'],
                    ['name' => 'tag_text', 'type' => 'text', 'label' => 'Top Tagline'],
                    ['name' => 'title_line_1', 'type' => 'text', 'label' => 'Title Line 1'],
                    ['name' => 'title_line_2', 'type' => 'textarea', 'label' => 'Title Line 2 (Gradient)'],
                    ['name' => 'subtitle', 'type' => 'text', 'label' => 'Highlight Subtitle'],
                    ['name' => 'description', 'type' => 'textarea', 'label' => 'Long Description'],
                    ['name' => 'date_text', 'type' => 'text', 'label' => 'Event Date Display'],
                    ['name' => 'venue_text', 'type' => 'text', 'label' => 'Event Venue Display'],
                    ['name' => 'btn1_text', 'type' => 'text', 'label' => 'Primary Button Text'],
                    ['name' => 'btn1_url', 'type' => 'text', 'label' => 'Primary Button URL'],
                    ['name' => 'btn2_text', 'type' => 'text', 'label' => 'Secondary Button Text'],
                    ['name' => 'btn2_url', 'type' => 'text', 'label' => 'Secondary Button URL'],
                    ['name' => 'stat1_val', 'type' => 'text', 'label' => 'Statistics 1 Value'],
                    ['name' => 'stat1_label', 'type' => 'text', 'label' => 'Statistics 1 Label'],
                    ['name' => 'stat2_val', 'type' => 'text', 'label' => 'Statistics 2 Value'],
                    ['name' => 'stat2_label', 'type' => 'text', 'label' => 'Statistics 2 Label'],
                    ['name' => 'stat3_val', 'type' => 'text', 'label' => 'Statistics 3 Value'],
                    ['name' => 'stat3_label', 'type' => 'text', 'label' => 'Statistics 3 Label'],
                    ['name' => 'stat4_val', 'type' => 'text', 'label' => 'Statistics 4 Value'],
                    ['name' => 'stat4_label', 'type' => 'text', 'label' => 'Statistics 4 Label'],
                ],
            ]
        );
    }
}

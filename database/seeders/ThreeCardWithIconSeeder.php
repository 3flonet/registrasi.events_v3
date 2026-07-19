<?php

namespace Database\Seeders;

use App\Models\SectionTemplate;
use Illuminate\Database\Seeder;

class ThreeCardWithIconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, check if the old slug exists and update it or create new
        SectionTemplate::where('slug', 'info-columns-sporty')->delete();

        SectionTemplate::updateOrCreate(
            ['slug' => '3-card-with-icon'],
            [
                'name' => ['en' => '3 Card with Icon', 'id' => '3 Kartu dengan Ikon'],
                'html_content' => '<section class="gr-about-section" id="about">
    <div class="gr-about-wrapper">
        <div class="gr-about-header">
            <span class="gr-about-pretitle">{{ $pre_title }}</span>
            <h2 class="gr-about-title">{{ $section_title }}</h2>
        </div>

        <div class="gr-about-grid">
            
            {{-- Item 1 --}}
            <div class="gr-exp-card group">
                <div class="gr-exp-icon-box">
                    <div class="gr-exp-icon">{{ $c1_icon }}</div>
                </div>
                <h3 class="gr-exp-title">{{ $c1_title }}</h3>
                <p class="gr-exp-desc">{{ $c1_desc }}</p>
            </div>

            {{-- Item 2 --}}
            <div class="gr-exp-card group">
                <div class="gr-exp-icon-box">
                    <div class="gr-exp-icon">{{ $c2_icon }}</div>
                </div>
                <h3 class="gr-exp-title">{{ $c2_title }}</h3>
                <p class="gr-exp-desc">{{ $c2_desc }}</p>
            </div>

            {{-- Item 3 --}}
            <div class="gr-exp-card group">
                <div class="gr-exp-icon-box">
                    <div class="gr-exp-icon">{{ $c3_icon }}</div>
                </div>
                <h3 class="gr-exp-title">{{ $c3_title }}</h3>
                <p class="gr-exp-desc">{{ $c3_desc }}</p>
            </div>

        </div>
    </div>
</section>',
                'css_content' => '/* Container */
.gr-about-section {
    padding: 6rem 0; background-color: white;
    font-family: "Plus Jakarta Sans", sans-serif;
}

.gr-about-wrapper { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }

/* Header */
.gr-about-header { text-align: center; margin-bottom: 4rem; }

.gr-about-pretitle {
    color: #10b981; /* primary */
    font-weight: 700; letter-spacing: 0.1em; font-size: 0.75rem; text-transform: uppercase;
}

.gr-about-title {
    font-family: "Teko", sans-serif;
    font-size: 2.5rem; /* 4xl */
    font-weight: 700; color: #0f172a; margin-top: 0.5rem;
    text-transform: uppercase; font-style: italic;
}
@media (min-width: 768px) { .gr-about-title { font-size: 3rem; } }

/* Grid */
.gr-about-grid {
    display: grid; gap: 2rem;
    grid-template-columns: 1fr;
}
@media (min-width: 768px) { .gr-about-grid { grid-template-columns: repeat(3, 1fr); } }

/* Card */
.gr-exp-card {
    background-color: #f8fafc; /* slate-50 */
    padding: 2rem; border-radius: 1.5rem;
    border: 1px solid #f1f5f9; cursor: pointer;
    transition: all 0.3s ease;
}
.gr-exp-card:hover { background-color: #ecfdf5; /* greenrun-light */ }

.gr-exp-icon-box {
    width: 3.5rem; height: 3.5rem; background-color: white;
    border-radius: 9999px; display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
    transition: transform 0.3s;
}
.group:hover .gr-exp-icon-box { transform: scale(1.1); }

.gr-exp-icon { font-size: 1.875rem; /* 3xl */ }

.gr-exp-title {
    font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;
    transition: color 0.3s;
}
.group:hover .gr-exp-title { color: #064e3b; /* dark emerald */ }

.gr-exp-desc {
    font-size: 0.875rem; color: #64748b; line-height: 1.6;
}',
                'fields' => [
                    ['name' => 'pre_title', 'type' => 'text', 'label' => 'Pre Title (Small Text)'],
                    ['name' => 'section_title', 'type' => 'text', 'label' => 'Main Section Title'],
                    ['name' => 'c1_icon', 'type' => 'text', 'label' => 'Column 1 Icon/Emoji'],
                    ['name' => 'c1_title', 'type' => 'text', 'label' => 'Column 1 Title'],
                    ['name' => 'c1_desc', 'type' => 'textarea', 'label' => 'Column 1 Description'],
                    ['name' => 'c2_icon', 'type' => 'text', 'label' => 'Column 2 Icon/Emoji'],
                    ['name' => 'c2_title', 'type' => 'text', 'label' => 'Column 2 Title'],
                    ['name' => 'c2_desc', 'type' => 'textarea', 'label' => 'Column 2 Description'],
                    ['name' => 'c3_icon', 'type' => 'text', 'label' => 'Column 3 Icon/Emoji'],
                    ['name' => 'c3_title', 'type' => 'text', 'label' => 'Column 3 Title'],
                    ['name' => 'c3_desc', 'type' => 'textarea', 'label' => 'Column 3 Description'],
                ],
            ]
        );
    }
}

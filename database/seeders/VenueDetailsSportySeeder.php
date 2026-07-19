<?php

namespace Database\Seeders;

use App\Models\SectionTemplate;
use Illuminate\Database\Seeder;

class VenueDetailsSportySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SectionTemplate::updateOrCreate(
            ['slug' => 'venue-details-sporty'],
            [
                'name' => ['en' => 'Venue Details Sporty', 'id' => 'Detail Lokasi Sporty'],
                'html_content' => '<section class="gr-venue-section">
    <div class="gr-venue-wrapper">
        <div class="gr-venue-grid">
            
            {{-- Left Column: Image with Overlay --}}
            <div class="gr-venue-visual">
                <div class="gr-visual-container shadow-2xl">
                    <img src="{{ $venue_image_url }}" alt="Venue" class="gr-venue-img">
                    <div class="gr-visual-overlay">
                        <div class="gr-overlay-content">
                            <span class="gr-overlay-tag">{{ $pre_title_overlay }}</span>
                            <h3 class="gr-overlay-title">{{ $venue_name_short }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Information --}}
            <div class="gr-venue-info">
                <span class="gr-info-badge">{{ $top_badge }}</span>
                <h2 class="gr-info-title">{{ $main_title }}</h2>
                <p class="gr-info-desc">{{ $description }}</p>

                <div class="gr-info-cards">
                    <div class="gr-mini-card">
                        <p class="gr-mini-val">{{ $info1_val }}</p>
                        <p class="gr-mini-label">{{ $info1_label }}</p>
                    </div>
                    <div class="gr-mini-card">
                        <p class="gr-mini-val">{{ $info2_val }}</p>
                        <p class="gr-mini-label">{{ $info2_label }}</p>
                    </div>
                </div>

                <a href="{{ $map_url }}" target="_blank" class="gr-map-link">
                    {{ $map_text }} <span class="ml-1">→</span>
                </a>
            </div>

        </div>
    </div>
</section>',
                'css_content' => '@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Teko:wght@400;500;600;700&display=swap");

.gr-venue-section {
    padding: 8rem 0; background-color: white;
    font-family: "Plus Jakarta Sans", sans-serif;
}

.gr-venue-wrapper { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }

.gr-venue-grid {
    display: grid; gap: 4rem; align-items: center;
}
@media (min-width: 1024px) { .gr-venue-grid { grid-template-columns: 1fr 1fr; } }

/* Visual Column */
.gr-visual-container {
    position: relative; border-radius: 2.5rem; overflow: hidden;
    aspect-ratio: 4/3;
}
@media (min-width: 1024px) { .gr-visual-container { aspect-ratio: 16/10; } }

.gr-venue-img { width: 100%; height: 100%; object-fit: cover; }

.gr-visual-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(15, 23, 42, 0.8), transparent 40%);
    display: flex; align-items: flex-end; padding: 2.5rem;
}

.gr-overlay-tag {
    color: #a3e635; /* volt */
    font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;
}

.gr-overlay-title {
    color: white; font-family: "Teko", sans-serif; font-size: 2.5rem; font-weight: 700;
    line-height: 1; margin-top: 0.25rem;
}

/* Info Column */
.gr-info-badge {
    display: inline-block; padding: 0.4rem 1rem; background-color: #f1f5f9;
    color: #64748b; font-size: 0.65rem; font-weight: 800;
    border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2rem;
}

.gr-info-title {
    font-family: "Teko", sans-serif; font-size: 3.5rem; font-weight: 700;
    color: #0f172a; line-height: 0.9; text-transform: uppercase; font-style: italic; margin-bottom: 2rem;
}

.gr-info-desc {
    color: #64748b; font-size: 1rem; line-height: 1.6; margin-bottom: 3rem; max-width: 32rem;
}

.gr-info-cards {
    display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 3rem;
}

.gr-mini-card {
    background-color: white; border: 1px solid #f1f5f9; padding: 1.5rem 2rem;
    border-radius: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.gr-mini-val { font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1; }
.gr-mini-label { font-size: 0.65rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-top: 0.25rem; }

.gr-map-link {
    display: inline-flex; align-items: center; color: #10b981;
    font-weight: 800; font-size: 0.875rem; text-decoration: none;
    transition: all 0.3s;
}
.gr-map-link:hover { color: #059669; transform: translateX(5px); }',
                'fields' => [
                    ['name' => 'venue_image_url', 'type' => 'image', 'label' => 'Venue Image'],
                    ['name' => 'pre_title_overlay', 'type' => 'text', 'label' => 'Overlay Tag (e.g. THE VENUE)'],
                    ['name' => 'venue_name_short', 'type' => 'text', 'label' => 'Overlay Title (e.g. Spark Senayan)'],
                    ['name' => 'top_badge', 'type' => 'text', 'label' => 'Info Badge (e.g. CENTRAL LOCATION)'],
                    ['name' => 'main_title', 'type' => 'text', 'label' => 'Main Section Title'],
                    ['name' => 'description', 'type' => 'textarea', 'label' => 'Description Paragraph'],
                    ['name' => 'info1_val', 'type' => 'text', 'label' => 'Info 1 Value (e.g. 06:00)'],
                    ['name' => 'info1_label', 'type' => 'text', 'label' => 'Info 1 Label (e.g. RACE START)'],
                    ['name' => 'info2_val', 'type' => 'text', 'label' => 'Info 2 Value (e.g. Outdoor)'],
                    ['name' => 'info2_label', 'type' => 'text', 'label' => 'Info 2 Label (e.g. RACE FORMAT)'],
                    ['name' => 'map_url', 'type' => 'text', 'label' => 'Google Maps URL'],
                    ['name' => 'map_text', 'type' => 'text', 'label' => 'Link Text (e.g. View on Google Maps)'],
                ],
            ]
        );
    }
}

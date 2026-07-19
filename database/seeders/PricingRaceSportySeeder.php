<?php

namespace Database\Seeders;

use App\Models\SectionTemplate;
use Illuminate\Database\Seeder;

class PricingRaceSportySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SectionTemplate::updateOrCreate(
            ['slug' => 'pricing-race-sporty'],
            [
                'name' => ['en' => 'Pricing Race Sporty', 'id' => 'Paket Lomba Sporty'],
                'html_content' => '<section class="gr-pricing-section">
    <div class="gr-pricing-wrapper">
        <div class="gr-pricing-header">
            <span class="gr-pricing-pretitle">{{ $pre_title }}</span>
            <h2 class="gr-pricing-title">{{ $main_title }}</h2>
            <p class="gr-pricing-subtitle">{{ $subtitle }}</p>
        </div>

        <div class="gr-pricing-grid">
            @if(is_array($pricing_list))
                @foreach($pricing_list as $group)
                    @if(isset($group["logos"]) && is_array($group["logos"]))
                        @foreach($group["logos"] as $card)
                        <div class="gr-price-card {{ ($card["url"] ?? "") == "featured" ? "is-featured" : "" }}">
                            @if(($card["url"] ?? "") == "featured")
                                <div class="gr-card-highlight">{{ $group["category_name"] ?: "RECOMMENDED FOR RUNNERS" }}</div>
                            @endif
                            
                            <div class="gr-card-inner">
                                <div class="gr-card-header">
                                    <div>
                                        <h3 class="gr-plan-name">{{ $card["name"] ?? "" }}</h3>
                                        <p class="gr-plan-desc">{{ $card["desc"] ?? "" }}</p>
                                    </div>
                                    @if(!empty($card["badge"]))
                                        <span class="gr-plan-badge" {{ ($card["url"] ?? "") == "featured" ? "style=background-color:#10b981;color:white" : "" }}>{{ $card["badge"] }}</span>
                                    @endif
                                </div>

                                <div class="gr-price-box">
                                    <span class="gr-currency">Rp</span>
                                    <span class="gr-amount">{{ $card["price"] ?? "0" }}</span>
                                    <span class="gr-suffix">.000 / pax</span>
                                </div>

                                <div class="gr-features">
                                    @php $features = explode("\n", $card["features"] ?? ""); @endphp
                                    @foreach($features as $f)
                                        @if(!empty(trim($f)))
                                        <div class="gr-feature-item">
                                            <div class="gr-feature-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-volt"><path d="M13 10V3L4 14H11V21L20 10H13Z"/></svg>
                                            </div>
                                            <div class="gr-feature-text">{{ $f }}</div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                @endforeach
            @endif
        </div>

        <div class="gr-pricing-footer">
            <a href="{{ $footer_btn_url }}" class="gr-footer-btn">
                {{ $footer_btn_text }}
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="ml-2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
            <p class="gr-footer-note">{{ $footer_note }}</p>
        </div>
    </div>
</section>',
                'css_content' => '@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Teko:wght@400;500;600;700&display=swap");

.gr-pricing-section {
    padding: 8rem 0; background-color: #0f172a;
    font-family: "Plus Jakarta Sans", sans-serif; color: white;
}

.gr-pricing-wrapper { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }

.gr-pricing-header { text-align: center; margin-bottom: 5rem; }

.gr-pricing-pretitle {
    color: #a3e635; /* volt */
    font-weight: 800; letter-spacing: 0.1em; font-size: 0.75rem; text-transform: uppercase;
}

.gr-pricing-title {
    font-family: "Teko", sans-serif; font-size: 5rem; font-weight: 700; 
    color: white; margin-top: 0.5rem; text-transform: uppercase; font-style: italic; line-height: 1;
}

.gr-pricing-subtitle {
    color: #94a3b8; font-size: 1.125rem; margin-top: 1.5rem; max-width: 32rem; margin-left: auto; margin-right: auto;
}

/* Grid */
.gr-pricing-grid {
    display: grid; gap: 2rem;
    grid-template-columns: 1fr;
}
@media (min-width: 768px) { .gr-pricing-grid { grid-template-columns: repeat(2, 1fr); max-width: 60rem; margin-left: auto; margin-right: auto; } }

/* Card */
.gr-price-card {
    background-color: #1a233a; border: 1px solid #334155;
    border-radius: 2rem; position: relative; overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.gr-price-card.is-featured {
    border-color: #10b981; box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25);
}

.gr-card-highlight {
    background-color: #10b981; color: #064e3b; padding: 0.75rem;
    text-align: center; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
}

.gr-card-inner { padding: 3rem; }

.gr-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; }

.gr-plan-name { font-size: 3rem; font-family: "Teko", sans-serif; font-weight: 700; line-height: 1; }
.gr-plan-desc { font-size: 0.75rem; color: #94a3b8; font-weight: 500; margin-top: 0.25rem; }

.gr-plan-badge {
    background-color: #a3e635; color: #064e3b; padding: 0.4rem 0.75rem;
    border-radius: 0.5rem; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
}

.gr-price-box { display: flex; align-items: baseline; margin-bottom: 2.5rem; border-bottom: 1px solid #334155; padding-bottom: 2rem; }
.gr-currency { font-size: 3.5rem; font-family: "Teko", sans-serif; font-weight: 700; color: #a3e635; margin-right: 0.5rem; }
.gr-amount { font-size: 6rem; font-family: "Teko", sans-serif; font-weight: 700; color: #a3e635; line-height: 1; }
.gr-suffix { color: #64748b; font-size: 0.875rem; font-weight: 600; margin-left: 0.5rem; text-transform: uppercase; }

.gr-features { display: grid; gap: 1.25rem; }
.gr-feature-item { display: flex; align-items: center; gap: 1rem; }
.gr-feature-icon {
    width: 2.5rem; height: 2.5rem; background-color: #1e293b; 
    border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;
}
.text-volt { color: #a3e635; }
.gr-feature-text { font-size: 0.875rem; font-weight: 700; color: white; }

/* Footer */
.gr-pricing-footer { text-align: center; margin-top: 5rem; }
.gr-footer-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 1.5rem 4rem; background-color: #a3e635; color: #064e3b;
    font-weight: 800; font-size: 1.5rem; border-radius: 9999px;
    text-decoration: none; text-transform: uppercase; font-family: "Teko", sans-serif;
    box-shadow: 0 20px 25px -5px rgba(163, 230, 53, 0.3);
    transition: all 0.3s;
}
.gr-footer-btn:hover { transform: translateY(-5px); box-shadow: 0 30px 35px -5px rgba(163, 230, 53, 0.4); }

.gr-footer-note { font-size: 0.75rem; color: #64748b; font-weight: 500; margin-top: 2rem; }',
                'fields' => [
                    ['name' => 'pre_title', 'type' => 'text', 'label' => 'Pre Title'],
                    ['name' => 'main_title', 'type' => 'text', 'label' => 'Main Title'],
                    ['name' => 'subtitle', 'type' => 'text', 'label' => 'Subtitle'],
                    ['name' => 'pricing_list', 'type' => 'repeater', 'label' => 'Pricing Cards'],
                    ['name' => 'footer_btn_text', 'type' => 'text', 'label' => 'Button Text'],
                    ['name' => 'footer_btn_url', 'type' => 'text', 'label' => 'Button URL'],
                    ['name' => 'footer_note', 'type' => 'text', 'label' => 'Bottom Note'],
                ],
            ]
        );
    }
}

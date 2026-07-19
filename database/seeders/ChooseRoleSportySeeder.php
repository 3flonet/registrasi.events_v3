<?php

namespace Database\Seeders;

use App\Models\SectionTemplate;
use Illuminate\Database\Seeder;

class ChooseRoleSportySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SectionTemplate::updateOrCreate(
            ['slug' => 'choose-role-sporty'],
            [
                'name' => ['en' => 'Choose Role Sporty', 'id' => 'Pilih Peran Sporty'],
                'html_content' => '<section class="gr-role-section">
    <div class="gr-role-wrapper">
        <div class="gr-role-header text-center">
            <span class="gr-role-pretitle">{{ $pre_title }}</span>
            <h2 class="gr-role-title">{{ $main_title }}</h2>
            <p class="gr-role-subtitle">{{ $subtitle }}</p>
        </div>

        <div class="gr-role-grid">
            @if(is_array($role_list))
                @foreach($role_list as $group)
                    @if(isset($group["logos"]) && is_array($group["logos"]))
                        @foreach($group["logos"] as $role)
                        <div class="gr-role-card {{ ($role["url"] ?? "") == "active" ? "is-active" : "" }}">
                            @if(($role["url"] ?? "") == "active")
                                <div class="gr-card-badge">{{ $role["badge"] ?: "RECOMMENDED" }}</div>
                            @endif
                            <div class="gr-role-icon-box">
                                <i class="fas {{ $role["price"] ?: "fa-users" }}"></i>
                            </div>
                            <h3 class="gr-role-card-title">{{ $role["name"] }}</h3>
                            <p class="gr-role-card-desc">{{ $role["desc"] }}</p>
                        </div>
                        @endforeach
                    @endif
                @endforeach
            @endif
        </div>

        <div class="gr-role-footer text-center">
            <a href="{{ $btn_url }}" class="gr-btn-volt">
                {{ $btn_text }} <span class="ml-2">→</span>
            </a>
            <div class="mt-8">
                <a href="{{ $footer_link_url }}" class="gr-footer-link">{{ $footer_link_text }}</a>
            </div>
        </div>
    </div>
</section>',
                'css_content' => '@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Teko:wght@400;500;600;700&display=swap");

.gr-role-section {
    padding: 8rem 0; background-color: #0f172a;
    font-family: "Plus Jakarta Sans", sans-serif; color: white;
}

.gr-role-wrapper { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }

.gr-role-header { margin-bottom: 5rem; }

.gr-role-pretitle {
    color: #a3e635; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em;
}

.gr-role-title {
    font-family: "Teko", sans-serif; font-size: 5rem; font-weight: 700;
    text-transform: uppercase; font-style: italic; line-height: 1; margin: 1rem 0;
}

.gr-role-subtitle {
    color: #94a3b8; font-size: 1rem; max-width: 32rem; margin: 0 auto;
}

.gr-role-grid {
    display: grid; gap: 2rem; margin-bottom: 5rem;
}
@media (min-width: 768px) { .gr-role-grid { grid-template-columns: repeat(3, 1fr); } }

.gr-role-card {
    background-color: #1e293b; padding: 3rem; border-radius: 2rem;
    border: 1px solid #334155; position: relative;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.gr-role-card.is-active {
    border-color: #10b981; background-color: rgba(16, 185, 129, 0.05);
    box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.15);
}

.gr-card-badge {
    position: absolute; top: 1.5rem; right: 1.5rem;
    background-color: #a3e635; color: #064e3b; padding: 0.3rem 0.75rem;
    border-radius: 0.5rem; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
}

.gr-role-icon-box {
    width: 4rem; height: 4rem; background-color: #0f172a; border-radius: 1rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; color: #a3e635; margin-bottom: 2.5rem;
}

.gr-role-card-title {
    font-family: "Teko", sans-serif; font-size: 2rem; font-weight: 700;
    text-transform: uppercase; font-style: italic; margin-bottom: 1rem;
}

.gr-role-card-desc {
    font-size: 0.875rem; color: #94a3b8; line-height: 1.6;
}

/* Footer Section */
.gr-btn-volt {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 1.25rem 3.5rem; background-color: #a3e635; color: #064e3b;
    font-weight: 800; font-size: 1.5rem; border-radius: 9999px;
    text-decoration: none; text-transform: uppercase; font-family: "Teko", sans-serif;
    box-shadow: 0 20px 25px -5px rgba(163, 230, 53, 0.3);
    transition: all 0.3s;
}
.gr-btn-volt:hover { transform: translateY(-5px); box-shadow: 0 30px 35px -5px rgba(163, 230, 53, 0.4); }

.gr-footer-link {
    font-size: 0.875rem; color: #64748b; text-decoration: none; font-weight: 600;
    transition: color 0.3s;
}
.gr-footer-link:hover { color: #10b981; }',
                'fields' => [
                    ['name' => 'pre_title', 'type' => 'text', 'label' => 'Pre Title (CHOOSE YOUR ROLE)'],
                    ['name' => 'main_title', 'type' => 'text', 'label' => 'Main Center Title'],
                    ['name' => 'subtitle', 'type' => 'textarea', 'label' => 'Subtitle text'],
                    ['name' => 'role_list', 'type' => 'repeater', 'label' => 'Role Options'],
                    ['name' => 'btn_text', 'type' => 'text', 'label' => 'Primary Button Text'],
                    ['name' => 'btn_url', 'type' => 'text', 'label' => 'Primary Button URL'],
                    ['name' => 'footer_link_text', 'type' => 'text', 'label' => 'Bottom Link Text'],
                    ['name' => 'footer_link_url', 'type' => 'text', 'label' => 'Bottom Link URL'],
                ],
            ]
        );
    }
}

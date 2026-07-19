<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SectionTemplate;
use App\Models\CustomSection;
use App\Models\WelcomeSection;
use Illuminate\Support\Str;

class MosaicGridSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Blueprint Configuration
        $fields = [
            ['name' => 'title', 'type' => 'text', 'label' => 'Main Title'],
            ['name' => 'description', 'type' => 'textarea', 'label' => 'Description Paragraph'],
            ['name' => 'button_text', 'type' => 'text', 'label' => 'Button Text'],
        ];

        for ($i = 1; $i <= 16; $i++) {
            $fields[] = ['name' => "image_$i", 'type' => 'text', 'label' => "Grid Image $i"];
        }

        $template = SectionTemplate::updateOrCreate(
            ['slug' => 'mosaic-grid-hero'],
            [
                'name' => [
                    'en' => 'Total Success Mosaic Grid',
                    'id' => 'Grid Mosaik Total Success'
                ],
                'fields' => $fields,
                'html_content' => $this->getHtmlContent(),
                'css_content' => $this->getCssContent(),
            ]
        );

        // 2. Localized Content
        // (Content remains consistent)
        $enContent = [
            'title' => "Total Success",
            'description' => "Enterprise and large-scale events cannot run on disconnected systems. If your event workflow feels fragmented, choose an event tech suite that brings everything together.",
            'button_text' => "Book A Demo",
        ];
        
        $idContent = [
            'title' => "Kesuksesan Total",
            'description' => "Event skala besar tidak bisa berjalan pada sistem yang terputus. Jika alur kerja event Anda terasa terfragmentasi, pilihlah paket teknologi event yang menyatukan semuanya.",
            'button_text' => "Pesan Demo",
        ];

        for ($i = 1; $i <= 16; $i++) {
            $url = "https://godreamcast-content.s3.ap-south-1.amazonaws.com/dreamcast/home/dc-grid/dc-$i.png";
            $enContent["image_$i"] = $url;
            $idContent["image_$i"] = $url;
        }

        $customSection = CustomSection::updateOrCreate(
            ['section_template_id' => $template->id],
            [
                'content' => [
                    'en' => $enContent,
                    'id' => $idContent,
                ]
            ]
        );

        // 3. Registering the Section
        WelcomeSection::updateOrCreate(
            ['name->en' => 'Total Success Mosaic'],
            [
                'custom_section_id' => $customSection->id,
                'is_visible' => true,
                'order' => 1,
            ]
        );
    }

    private function getHtmlContent()
    {
        return <<<'HTML'
<section class="mosaic-hero-wrapper py-20 lg:py-32 text-white overflow-hidden">
    {{-- Header Content --}}
    <div class="header-container text-center mb-16 md:mb-20 px-6">
        <h1 class="text-6xl md:text-8xl font-black mb-6 tracking-tighter animate-fade-in-up uppercase">
            {{ $title }}
        </h1>
        <p class="text-white/90 text-[.9rem] md:text-lg max-w-2xl mx-auto mb-10 leading-relaxed font-semibold animate-fade-in-up delay-100">
            {{ $description }}
        </p>
        <div class="animate-fade-in-up delay-200">
            <button class="book-demo-btn">
                {{ $button_text }}
            </button>
        </div>
    </div>

    {{-- The Mosaic Grid Container --}}
    <div class="mosaic-grid-container px-4">
        <div class="flex flex-row justify-center gap-3 md:gap-5 lg:gap-6 w-full">
            
            {{-- Column 1: Only Desktop --}}
            <div class="hidden md:flex flex-col flex-1 basis-[18%] justify-end gap-3 md:gap-5 lg:gap-6">
                <img src="{{ $image_1 }}" alt="Asset 1" class="mosaic-img" loading="lazy">
                <img src="{{ $image_2 }}" alt="Asset 2" class="mosaic-img" loading="lazy">
                <img src="{{ $image_3 }}" alt="Asset 3" class="mosaic-img" loading="lazy">
            </div>

            {{-- Column 2 --}}
            <div class="flex flex-col flex-1 justify-end gap-3 md:gap-5 lg:gap-6">
                <img src="{{ $image_4 }}" alt="Asset 4" class="mosaic-img" loading="lazy">
                <img src="{{ $image_5 }}" alt="Asset 5" class="mosaic-img" loading="lazy">
                <img src="{{ $image_6 }}" alt="Asset 6" class="mosaic-img" loading="lazy">
            </div>

            {{-- Column 3 --}}
            <div class="flex flex-col flex-1 justify-end gap-3 md:gap-5 lg:gap-6">
                <img src="{{ $image_7 }}" alt="Asset 7" class="mosaic-img" loading="lazy">
                <img src="{{ $image_8 }}" alt="Asset 8" class="mosaic-img" loading="lazy">
                <img src="{{ $image_9 }}" alt="Asset 9" class="mosaic-img" loading="lazy">
            </div>

            {{-- Column 4 --}}
            <div class="flex flex-col flex-1 justify-end gap-3 md:gap-5 lg:gap-6">
                <img src="{{ $image_10 }}" alt="Asset 10" class="mosaic-img" loading="lazy">
                <img src="{{ $image_11 }}" alt="Asset 11" class="mosaic-img" loading="lazy">
                <img src="{{ $image_12 }}" alt="Asset 12" class="mosaic-img" loading="lazy">
            </div>

            {{-- Column 5: Only Desktop --}}
            <div class="hidden md:flex flex-col flex-1 basis-[18%] justify-end gap-3 md:gap-5 lg:gap-6">
                <img src="{{ $image_13 }}" alt="Asset 13" class="mosaic-img" loading="lazy">
                <img src="{{ $image_14 }}" alt="Asset 14" class="mosaic-img" loading="lazy">
                <img src="{{ $image_15 }}" alt="Asset 15" class="mosaic-img" loading="lazy">
                <img src="{{ $image_16 }}" alt="Asset 16" class="mosaic-img" loading="lazy">
            </div>
        </div>
        
        {{-- Image 16 at the BOTTOM for Mobile Only --}}
        <div class="grid grid-cols-2 gap-3 mt-3 md:hidden">
            <div class="col-start-2"> {{-- Align to the right side to match Column 4's end --}}
                <img src="{{ $image_16 }}" alt="Asset 16" class="mosaic-img" loading="lazy">
            </div>
        </div>
    </div>
</section>
HTML;
    }

    private function getCssContent()
    {
        return <<<'CSS'
.mosaic-hero-wrapper {
    background-color: #4f18d3;
    width: 100%;
}
.header-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 1.5rem;
}
.mosaic-grid-container {
    max-width: 1300px;
    margin-left: auto;
    margin-right: auto;
    padding: 0 2rem;
}
.mosaic-img {
    width: 100%;
    height: auto;
    border-radius: 0.8rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    transition: transform 0.5s ease;
}
.mosaic-img:hover {
    transform: scale(1.03);
}
.book-demo-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0.8rem;
    padding: 0.8rem 2.2rem;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    color: #4f18d3;
    background-color: #f8f9fa;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    margin-bottom: 2rem;
    border: none;
    cursor: pointer;
}
.book-demo-btn:hover {
    transform: translateY(-2px);
    background-color: #9fcfff;
    box-shadow: 0 10px 20px -5px rgba(0,0,0,0.3);
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }

@media (max-width: 768px) {
    .mosaic-grid-container {
        padding: 0 1rem;
    }
    .header-container h1 {
        font-size: 3.5rem;
    }
}
CSS;
    }
}

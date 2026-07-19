<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SectionTemplate;
use App\Models\CustomSection;
use App\Models\WelcomeSection;
use Illuminate\Support\Str;

class HeroBentoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Section Template (The Blueprint)
        $template = SectionTemplate::updateOrCreate(
            ['slug' => 'bento-hero-section'],
            [
                'name' => [
                    'en' => 'Bento Hero Section',
                    'id' => 'Seksi Hero Bento'
                ],
                'fields' => [
                    ['name' => 'subtitle', 'type' => 'text', 'label' => 'Sub Title (Small text on top)'],
                    ['name' => 'title', 'type' => 'text', 'label' => 'Main Title (Large header)'],
                    ['name' => 'description', 'type' => 'textarea', 'label' => 'Description Paragraph'],
                    ['name' => 'person_image', 'type' => 'text', 'label' => 'Person Image URL'],
                ],
                'html_content' => $this->getHtmlContent(),
                'css_content' => $this->getCssContent(),
            ]
        );

        // 2. Create an Instance (Custom Section)
        $customSection = CustomSection::create([
            'section_template_id' => $template->id,
            'content' => [
                'en' => [
                    'subtitle' => "Empower Your Event Growth with Dreamcast's",
                    'title' => "Event Registration Platform & Solutions",
                    'description' => "With our event registration platform & solution, you can bring convenience to all formats of events. Capture valuable data and deploy easy check-ins with self-check-in kiosks and volunteer-based services to maximize the success and revenue of your events.",
                    'person_image' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=1000&auto=format&fit=crop',
                ],
                'id' => [
                    'subtitle' => "Tingkatkan Pertumbuhan Event Anda bersama Dreamcast",
                    'title' => "Platform & Solusi Registrasi Event",
                    'description' => "Dengan platform & solusi registrasi acara kami, Anda dapat menghadirkan kenyamanan bagi semua format acara. Tangkap data berharga dan terapkan lapor masuk yang mudah dengan kios lapor masuk mandiri dan layanan berbasis sukarelawan untuk memaksimalkan kesuksesan dan pendapatan acara Anda.",
                    'person_image' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=1000&auto=format&fit=crop',
                ]
            ]
        ]);

        // 3. Register to Welcome Page
        WelcomeSection::updateOrCreate(
             ['name->en' => 'Hero Feature Section'],
             [
                'custom_section_id' => $customSection->id,
                'is_visible' => true,
                'order' => 2, // Shifted to make room for the new one at 1 if needed, or keep at top
             ]
        );
    }

    private function getHtmlContent()
    {
        return <<<'HTML'
<section class="py-12 md:py-24 px-4 md:px-6 bg-[#fffbf9] overflow-hidden">
    <div class="max-w-7xl mx-auto text-center mb-12 md:mb-20">
        <p class="text-[#6d5dfc] font-black uppercase tracking-[0.3em] text-[8px] md:text-[10px] mb-4">{{ $subtitle }}</p>
        <h1 class="text-3xl md:text-7xl font-[900] text-[#1a1235] mb-6 md:mb-10 tracking-tighter leading-[1.1] max-w-5xl mx-auto">
            {!! str_replace('&', '<span class="text-[#6d5dfc]">&</span>', $title) !!}
        </h1>
        <p class="text-gray-500 text-sm md:text-lg max-w-4xl mx-auto leading-relaxed font-semibold px-4">
            {{ $description }}
        </p>
    </div>

    <div class="max-w-6xl mx-auto relative px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4">
            <!-- Left Side -->
            <div class="hidden lg:flex lg:col-span-3 flex-col gap-4">
                <div class="bg-[#3b3486] rounded-[2.5rem] p-8 aspect-square flex flex-col justify-center text-white relative overflow-hidden group">
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-indigo-400/20 px-4 py-2 rounded-full text-[9px] font-black uppercase tracking-widest border border-white/10 flex items-center">Check-in <i class="fas fa-plus ml-2 text-[7px] bg-white text-[#3b3486] p-1 rounded-full"></i></span>
                        <div class="flex items-center gap-2">
                             <span class="bg-indigo-400/20 w-8 h-8 rounded-full border border-white/10 flex items-center justify-center text-[10px]"><i class="fas fa-plus"></i></span>
                             <span class="bg-indigo-400/20 px-6 py-2 rounded-full text-[9px] font-black uppercase tracking-widest border border-white/10">Badging</span>
                        </div>
                        <span class="bg-indigo-400/20 px-5 py-2 rounded-full text-[9px] font-black uppercase tracking-widest border border-white/10">Mobile First</span>
                    </div>
                </div>
                <div class="bg-[#fce7f3] rounded-[2.5rem] p-8 grow flex items-center justify-center relative overflow-hidden min-h-[160px]">
                    <div class="flex items-end gap-1.5 px-4">
                        <div class="w-2.5 bg-indigo-200 rounded-t-sm h-6"></div>
                        <div class="w-2.5 bg-yellow-400 rounded-t-sm h-12"></div>
                        <div class="w-2.5 bg-indigo-200 rounded-t-sm h-8"></div>
                        <div class="w-2.5 bg-green-400 rounded-t-sm h-14"></div>
                        <i class="fas fa-search text-4xl ml-2 text-[#1a1235]"></i>
                    </div>
                </div>
            </div>

            <!-- Center (Person) -->
            <div class="col-span-1 md:col-span-1 lg:col-span-6 relative group min-h-[400px] md:min-h-[500px]">
                 <div class="bg-gradient-to-br from-[#eb4899] to-[#6d5dfc] rounded-[3rem] h-full w-full overflow-hidden relative shadow-2xl">
                    <div class="absolute top-6 left-6 md:top-10 md:left-10 bg-white/20 backdrop-blur-xl rounded-2xl p-5 border border-white/30 text-white w-48 z-10 shadow-lg">
                        <p class="text-[8px] font-black uppercase tracking-widest mb-4 opacity-80">Registration</p>
                        <div class="space-y-2.5">
                             <div class="h-3 bg-white/40 rounded-full w-3/4"></div>
                             <div class="h-3 bg-white/30 rounded-full"></div>
                             <div class="h-3 bg-white/20 rounded-full w-5/6"></div>
                             <div class="h-8 bg-indigo-600/80 rounded-xl mt-4 flex items-center justify-center text-[8px] font-black uppercase tracking-widest">Submit</div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-12 h-12 bg-orange-400 rounded-full flex items-center justify-center shadow-lg border-4 border-white/10">
                            <i class="fas fa-arrow-up rotate-45 text-white"></i>
                        </div>
                    </div>
                    
                    <img src="{{ $person_image }}" class="absolute bottom-0 left-1/2 -translate-x-1/2 h-full w-full object-cover mix-blend-normal group-hover:scale-105 transition-transform duration-700" alt="Person">
                 </div>
            </div>

            <!-- Mobile Only Grid elements -->
            <div class="grid grid-cols-2 gap-4 lg:hidden">
                 <div class="bg-[#3b3486] rounded-3xl p-6 aspect-square flex flex-col justify-center text-white relative">
                     <span class="bg-white/10 px-3 py-1 rounded-full text-[8px] font-bold uppercase w-fit mb-2">Check-in</span>
                     <p class="text-[10px] font-bold text-white/80">Mobile First Badging</p>
                 </div>
                 <div class="bg-[#fce7f3] rounded-3xl p-6 aspect-square flex items-center justify-center">
                     <i class="fas fa-search text-3xl text-indigo-900"></i>
                 </div>
            </div>

            <!-- Right Side -->
            <div class="col-span-1 md:col-span-1 lg:col-span-3 flex flex-col gap-4">
                 <div class="grid grid-cols-2 gap-4">
                    <div class="bg-[#3b3486] rounded-3xl aspect-square flex items-center justify-center shadow-indigo-200 shadow-lg">
                        <i class="fas fa-bell text-white text-3xl animate-swing"></i>
                    </div>
                    <div class="bg-white rounded-3xl aspect-square flex items-center justify-center shadow-md">
                        <i class="fas fa-ticket-alt text-[#3b3486] text-3xl -rotate-45"></i>
                    </div>
                 </div>
                 <div class="bg-[#1a1235] rounded-[2.5rem] p-8 h-40 flex flex-col items-center justify-center text-white relative border border-white/5">
                    <i class="fas fa-qrcode text-5xl mb-2 opacity-50"></i>
                    <div class="absolute inset-x-8 h-0.5 bg-red-500 shadow-[0_0_15px_rgba(239,68,68,0.8)] animate-scan"></div>
                 </div>
                 <div class="bg-gradient-to-br from-[#f97316] to-[#ec4899] rounded-[2.5rem] p-8 aspect-square relative text-white flex flex-col justify-end overflow-hidden shadow-orange-100 shadow-xl grow min-h-[220px]">
                     <p class="text-[10px] font-black uppercase tracking-widest leading-none mb-2 opacity-80">Your trusted</p>
                     <p class="text-xl md:text-2xl font-black uppercase tracking-tight leading-tight">Event Registration Platform</p>
                     
                     <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 p-4 opacity-20 pointer-events-none">
                        <svg class="w-48 h-48 animate-pulse-slow" viewBox="0 0 100 100">
                             <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="0.3" />
                             <circle cx="50" cy="50" r="35" fill="none" stroke="currentColor" stroke-width="0.3" />
                             <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="0.3" />
                             <circle cx="50" cy="50" r="15" fill="none" stroke="currentColor" stroke-width="0.3" />
                        </svg>
                     </div>
                 </div>
            </div>
        </div>
    </div>
</section>
HTML;
    }

    private function getCssContent()
    {
        return <<<'CSS'
@keyframes scan {
    0% { transform: translateY(-30px); }
    100% { transform: translateY(30px); }
}
.animate-scan {
    animation: scan 2s linear infinite alternate;
}
@keyframes swing {
    0% { transform: rotate(0deg); }
    20% { transform: rotate(15deg); }
    40% { transform: rotate(-10deg); }
    60% { transform: rotate(5deg); }
    80% { transform: rotate(-5deg); }
    100% { transform: rotate(0deg); }
}
.animate-swing {
    animation: swing 2s ease-in-out infinite;
    transform-origin: top center;
}
@keyframes pulse-slow {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.1; }
    50% { transform: translate(-50%, -50%) scale(1.1); opacity: 0.3; }
}
.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}
CSS;
    }
}

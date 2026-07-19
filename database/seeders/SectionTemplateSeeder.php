<?php

namespace Database\Seeders;

use App\Models\SectionTemplate;
use Illuminate\Database\Seeder;

class SectionTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. HERO PREMIUM
        SectionTemplate::updateOrCreate(
            ['slug' => 'hero-premium'],
            [
                'name' => [
                    'en' => 'Premium Hero Section',
                    'id' => 'Hero Section Premium'
                ],
                'html_content' => '
<section class="relative bg-gray-900 border-b border-gray-800 overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <img src="{{ $image_url }}" class="w-full h-full object-cover" alt="Background">
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900 to-transparent"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="lg:w-2/3">
            <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight mb-6">
                {!! $title !!}
            </h1>
            <p class="text-xl text-gray-400 mb-10 max-w-2xl leading-relaxed">
                {{ $subtitle }}
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ $cta_url }}" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/25">
                    {{ $cta_text }}
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </a>
            </div>
        </div>
    </div>
</section>
                ',
                'css_content' => '',
                'fields' => [
                    ['name' => 'title', 'type' => 'text', 'label' => 'Headline (HTML allowed)'],
                    ['name' => 'subtitle', 'type' => 'textarea', 'label' => 'Sub-headline'],
                    ['name' => 'cta_text', 'type' => 'text', 'label' => 'Button Text'],
                    ['name' => 'cta_url', 'type' => 'text', 'label' => 'Button URL'],
                    ['name' => 'image_url', 'type' => 'text', 'label' => 'Background Image URL'],
                ]
            ]
        );

        // 2. TRUST STATS
        SectionTemplate::updateOrCreate(
            ['slug' => 'trust-stats'],
            [
                'name' => [
                    'en' => 'Trust Stats Counters',
                    'id' => 'Statistik Pencapaian'
                ],
                'html_content' => '
<section class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="p-8 rounded-3xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
                <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">{{ $stat1_num }}</div>
                <div class="text-gray-600 dark:text-gray-400 font-medium uppercase tracking-widest text-sm">{{ $stat1_label }}</div>
            </div>
            <div class="p-8 rounded-3xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
                <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">{{ $stat2_num }}</div>
                <div class="text-gray-600 dark:text-gray-400 font-medium uppercase tracking-widest text-sm">{{ $stat2_label }}</div>
            </div>
            <div class="p-8 rounded-3xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1">
                <div class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">{{ $stat3_num }}</div>
                <div class="text-gray-600 dark:text-gray-400 font-medium uppercase tracking-widest text-sm">{{ $stat3_label }}</div>
            </div>
        </div>
    </div>
</section>
                ',
                'css_content' => '',
                'fields' => [
                    ['name' => 'stat1_num', 'type' => 'text', 'label' => 'Stat 1 Number (e.g. 50+)'],
                    ['name' => 'stat1_label', 'type' => 'text', 'label' => 'Stat 1 Label'],
                    ['name' => 'stat2_num', 'type' => 'text', 'label' => 'Stat 2 Number'],
                    ['name' => 'stat2_label', 'type' => 'text', 'label' => 'Stat 2 Label'],
                    ['name' => 'stat3_num', 'type' => 'text', 'label' => 'Stat 3 Number'],
                    ['name' => 'stat3_label', 'type' => 'text', 'label' => 'Stat 3 Label'],
                ]
            ]
        );

        // 3. CTA SPLIT
        SectionTemplate::updateOrCreate(
            ['slug' => 'cta-split'],
            [
                'name' => [
                    'en' => 'Call to Action Split',
                    'id' => 'CTA Split Berwarna'
                ],
                'html_content' => '
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden bg-indigo-600 shadow-2xl">
            <div class="absolute inset-0 bg-indigo-700 opacity-50 mix-blend-multiply"></div>
            <div class="relative px-8 py-16 sm:px-16 sm:py-20 lg:flex lg:items-center">
                <div class="lg:w-0 lg:flex-1">
                    <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        {{ $headline }}
                    </h2>
                    <p class="mt-4 max-w-3xl text-lg text-indigo-100 leading-relaxed">
                        {{ $subheadline }}
                    </p>
                </div>
                <div class="mt-10 lg:mt-0 lg:ml-12 lg:flex-shrink-0">
                    <a href="{{ $button_url }}" class="w-full flex items-center justify-center px-10 py-4 border border-transparent text-base font-bold rounded-xl text-indigo-600 bg-white hover:bg-indigo-50 transition-all transform hover:scale-105 active:scale-95 shadow-lg">
                        {{ $button_text }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
                ',
                'css_content' => '',
                'fields' => [
                    ['name' => 'headline', 'type' => 'text', 'label' => 'Headline'],
                    ['name' => 'subheadline', 'type' => 'textarea', 'label' => 'Sub-headline'],
                    ['name' => 'button_text', 'type' => 'text', 'label' => 'Button Text'],
                    ['name' => 'button_url', 'type' => 'text', 'label' => 'Button URL'],
                ]
            ]
        );
    }
}

@props(['section'])

@if($section->customSection && $section->customSection->template)
    @php
        $template = $section->customSection->template;
        $allContent = $section->customSection->content;
        $currentLocale = app()->getLocale();
        $localeContent = $allContent[$currentLocale] ?? ($allContent['en'] ?? []);
        
        $htmlTemplate = $template->html_content;
        $cssTemplate  = $template->css_content;
    @endphp

    {{-- Render Custom CSS jika ada --}}
    @if(!empty($cssTemplate))
        @push('custom_styles')
        <style>
            /* Render CSS melalui Blade jika di dalamnya ada variabel */
            {!! \Illuminate\Support\Facades\Blade::render($cssTemplate, $localeContent) !!}
        </style>
        @endpush
    @endif

    {{-- Render HTML yang sudah digabungkan dengan Blade Engine --}}
    <div class="custom-section-wrapper">
        {!! \Illuminate\Support\Facades\Blade::render($htmlTemplate, $localeContent) !!}
    </div>

@endif
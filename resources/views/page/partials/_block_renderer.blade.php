@props(['block'])

@php
/**
 * RE-RENDERER ENGINE (ULTIMATE VERSION)
 * 1. Cari template berdasarkan slug
 */
$template = \App\Models\SectionTemplate::where('slug', $block['template_slug'])->first();
@endphp

@if($template)
    @php
        $content = $block['data'];
        $htmlTemplate = $template->html_content;
        $cssTemplate  = $template->css_content;

        /**
         * FORCE COMPILE BLADE (THE NUCLEAR OPTION)
         * Menggunakan Blade::compileString + eval() untuk memastikan 
         * direktif @if, @foreach, dll dieksekusi 100% dan tidak muncul di layar.
         */
        function renderBladeString($__php_string, $__php_data) {
            $__php_compiled = \Illuminate\Support\Facades\Blade::compileString($__php_string);
            
            // Siapkan output buffer untuk menangkap hasil eksekusi PHP
            ob_start();
            extract($__php_data);
            try {
                // Eksekusi kode PHP hasil kompilasi Blade
                eval('?>' . $__php_compiled);
            } catch (\Exception $e) {
                echo "<!-- Error during Blade rendering: " . $e->getMessage() . " -->";
            }
            return ob_get_clean();
        }

        $finalHtml = renderBladeString($htmlTemplate, $content);
        $finalCss  = !empty($cssTemplate) ? renderBladeString($cssTemplate, $content) : '';
    @endphp

    {{-- Render High-Level CSS Block --}}
    @if(!empty($finalCss))
        <style>
            {!! $finalCss !!}
        </style>
    @endif

    {{-- Render Final HTML --}}
    <div class="template-wrapper block-{{ $block['template_slug'] }}">
        {!! $finalHtml !!}
    </div>

@endif
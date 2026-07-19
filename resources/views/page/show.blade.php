<x-guest-layout>
    <div class="bg-gray-50 min-h-screen font-outfit">
        {{-- PAGE HEADER --}}
        <div class="bg-[#1a1235] pt-32 pb-20 px-6 text-center relative overflow-hidden">
            {{-- Decoration --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-accent/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl"></div>

            <div class="relative z-10 max-w-4xl mx-auto">
                 <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-4">
                    {{ $page->getTranslation('title', app()->getLocale()) }}
                 </h1>
                 <div class="h-1 w-20 bg-accent mx-auto rounded-full"></div>
            </div>
        </div>

        {{-- PAGE CONTENT --}}
        <div class="max-w-4xl mx-auto px-6 -mt-10 pb-24 relative z-20">
            <div class="bg-white rounded-3xl shadow-2xl shadow-indigo-900/5 p-8 md:p-16 border border-gray-100">
                <article class="prose prose-indigo max-w-none prose-h2:text-[#1a1235] prose-h2:font-black prose-h2:uppercase prose-h2:tracking-tight prose-h3:text-indigo-600 prose-h3:font-bold prose-p:text-gray-600 prose-p:leading-relaxed">
                    @php
                    $content = $page->getTranslation('content', app()->getLocale());
                    @endphp

                    @if(is_array($content))
                        @foreach($content as $block)
                            @include('page.partials._block_renderer', ['block' => $block])
                        @endforeach
                    @else
                        {!! \Illuminate\Support\Facades\Blade::render($content) !!}
                    @endif
                </article>

                <div class="mt-16 pt-8 border-t border-gray-50 flex items-center justify-between text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">
                    <span>Last Updated: {{ $page->updated_at->format('d M Y') }}</span>
                    <span>Registrasi.Events Legal</span>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Insights | {{ $event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        @media print {
            body { background: white !important; color: black !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .glass-card { 
                background: white !important; 
                border: 1px solid #e2e8f0 !important; 
                box-shadow: none !important;
                backdrop-filter: none !important;
                page-break-inside: avoid;
            }
            .bg-\[\#1a1235\] { background: white !important; }
            .fixed, .absolute.top-0, .-z-10 { display: none !important; } /* Hide decors */
            button, .pt-20, .mt-20 { display: none !important; } /* Hide actions */
            .text-white { color: #1a1235 !important; }
            .text-indigo-400 { color: #4f46e5 !important; }
            .mb-12 { margin-bottom: 1.5rem !important; }
            .glass-card { margin-bottom: 2rem; border-radius: 1.5rem !important; overflow: hidden !important; }
            .grid { display: block !important; } /* Force single column in print */
            .grid > div { width: 100% !important; margin-bottom: 2rem !important; }
            .apexcharts-canvas { margin: 0 auto !important; }
            .apexcharts-legend-text { font-size: 12px !important; color: #1a1235 !important; font-weight: 600 !important; }
            .apexcharts-datalabel-label { font-size: 14px !important; }
            .apexcharts-datalabel-value { font-size: 18px !important; }
        }
    </style>
</head>
<body class="bg-[#1a1235] min-h-screen relative overflow-x-hidden selection:bg-indigo-500 selection:text-white pb-20">
    
    <!-- Toast Notification -->
    <div id="toast" class="fixed top-10 left-1/2 -translate-x-1/2 z-[200] opacity-0 pointer-events-none transition-all duration-500 translate-y-[-20px]">
        <div class="glass-card px-8 py-4 rounded-2xl border border-indigo-500/30 flex items-center gap-4 shadow-2xl shadow-indigo-900/40">
            <div class="w-8 h-8 rounded-xl bg-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="fas fa-check text-xs"></i>
            </div>
            <p class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest" id="toast-message"></p>
        </div>
    </div>

    <script>
        function showToast(message) {
            const toast = document.getElementById('toast');
            const msgEl = document.getElementById('toast-message');
            msgEl.innerText = message;
            
            toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-[-20px]');
            toast.classList.add('opacity-100', 'translate-y-0');
            
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-[-20px]');
                toast.classList.remove('opacity-100', 'translate-y-0');
                setTimeout(() => toast.classList.add('pointer-events-none'), 500);
            }, 3000);
        }
    </script>
    <!-- Background Decor -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-[10%] -right-[10%] w-[50%] h-[50%] bg-indigo-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -left-[10%] w-[50%] h-[50%] bg-purple-600/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            
            <!-- Event OG Branding -->
            @if($event->hasMedia('og_image'))
            <div class="w-full h-48 md:h-64 rounded-[3rem] overflow-hidden mb-12 shadow-2xl shadow-indigo-900/20 border border-white/10 animate-fade-in relative group">
                <img src="{{ $event->getFirstMediaUrl('og_image') }}" alt="{{ $event->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-[#1a1235]/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-8 left-10">
                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-2">Event Branding</p>
                    <h2 class="text-2xl font-black text-white uppercase tracking-tighter">{{ $event->name }}</h2>
                </div>
            </div>
            @endif

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12 animate-fade-in">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 backdrop-blur-xl rounded-full border border-white/10 mb-6">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Real-time Analytics</span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-white uppercase tracking-tighter leading-none mb-4">
                        Feedback <span class="text-indigo-400">Insights</span>
                    </h1>
                    <div class="flex items-center gap-4">
                        <p class="text-gray-400 font-medium text-sm md:text-base uppercase tracking-widest">{{ $event->name }}</p>
                        <button onclick="navigator.clipboard.writeText(window.location.href); showToast('Results link copied to clipboard!')" class="px-4 py-2 bg-indigo-500/20 text-indigo-400 text-[9px] font-black uppercase tracking-widest rounded-full border border-indigo-500/30 hover:bg-indigo-500 hover:text-white transition-all">
                            <i class="fas fa-share-alt mr-2"></i> Share Results
                        </button>
                    </div>
                </div>

                <div class="glass-card rounded-3xl p-8 text-center min-w-[200px]">
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1">Total Submissions</p>
                    <h3 class="text-5xl font-black text-[#1a1235] tracking-tighter">{{ number_format($totalSubmissions) }}</h3>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-2">Active Responses</p>
                </div>
            </div>

            @if($totalSubmissions > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-fade-in" style="animation-delay: 0.2s">
                @foreach($aggregatedData as $fieldName => $data)
                @php 
                    $sanitizedFieldName = Str::slug($fieldName); 
                    $isFullWidth = in_array($data['type'], ['text', 'textarea']);
                @endphp
                
                <div class="glass-card rounded-[2rem] p-10 flex flex-col shadow-xl shadow-indigo-900/10 border-white/40 {{ $isFullWidth ? 'md:col-span-2' : '' }}">
                    <div class="flex items-start justify-between mb-8">
                        <div>
                            <h4 class="text-lg font-black text-[#1a1235] uppercase tracking-tight leading-tight mb-2">{{ $data['label'] }}</h4>
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-500 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                {{ $data['total_responses'] }} Samples
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-indigo-500 shadow-sm border border-gray-100">
                            @if($data['type'] === 'rating') <i class="fas fa-star text-sm"></i>
                            @elseif($data['type'] === 'radio' || $data['type'] === 'select') <i class="fas fa-chart-pie text-sm"></i>
                            @else <i class="fas fa-comment-alt text-sm"></i> @endif
                        </div>
                    </div>

                    <div class="flex-grow flex items-center justify-center min-h-[300px]">
                        @if(in_array($data['type'], ['radio', 'select', 'checkbox']))
                            <div id="chart-{{ $sanitizedFieldName }}" class="w-full"></div>
                            <script>
                                (function() {
                                    var options = {
                                        series: @json($data['data']['values']),
                                        chart: { type: 'donut', height: 280 },
                                        labels: @json($data['data']['labels']),
                                        colors: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#f43f5e'],
                                        legend: { position: 'bottom', labels: { colors: '#64748b', useSeriesColors: false }, fontSize: '12px', fontWeight: 600 },
                                        plotOptions: {
                                            pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'TOTAL', color: '#1a1235', fontSize: '14px', fontWeight: 900 } } } }
                                        },
                                        stroke: { show: false }
                                    };
                                    new ApexCharts(document.querySelector("#chart-{{ $sanitizedFieldName }}"), options).render();
                                })();
                            </script>
                        @elseif($data['type'] === 'rating')
                            <div class="text-center w-full">
                                <div class="relative inline-block mb-4">
                                    <h3 class="text-8xl font-black text-indigo-600 tracking-tighter leading-none">{{ number_format($data['data']['average'], 1) }}</h3>
                                    <div class="flex justify-center gap-1 text-amber-400 text-lg mt-2">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="fas fa-star {{ $i <= round($data['data']['average']) ? '' : 'text-gray-100' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8">Average Satisfaction</p>
                                
                                <div class="space-y-3 px-6 md:px-12">
                                    @foreach([5,4,3,2,1] as $star)
                                        @php 
                                            $count = $data['data']['distribution'][$star] ?? 0; 
                                            $perc = ($data['total_responses'] > 0) ? ($count / $data['total_responses'] * 100) : 0; 
                                            $label = match($star) {
                                                5 => 'Excellent',
                                                4 => 'Very Good',
                                                3 => 'Average',
                                                2 => 'Poor',
                                                1 => 'Terrible',
                                            };
                                        @endphp
                                        <div class="flex items-center gap-4">
                                            <div class="w-20 text-left">
                                                <span class="text-[8px] font-black text-[#1a1235] uppercase tracking-tighter block leading-none">{{ $label }}</span>
                                                <span class="text-[7px] font-bold text-gray-400 uppercase tracking-widest">{{ $star }} Stars</span>
                                            </div>
                                            <div class="flex-grow h-2 bg-gray-50 rounded-full overflow-hidden border border-gray-100">
                                                <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $perc }}%"></div>
                                            </div>
                                            <div class="w-10 text-right">
                                                <span class="text-[9px] font-black text-[#1a1235]">{{ round($perc) }}%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="w-full max-h-[300px] overflow-y-auto custom-scrollbar space-y-3 pr-2">
                                @foreach($data['data'] as $response)
                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                        <p class="text-xs text-gray-600 font-medium leading-relaxed italic">"{{ $response }}"</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="glass-card rounded-[3rem] p-24 text-center animate-fade-in">
                <div class="w-24 h-24 bg-white/5 rounded-[30px] flex items-center justify-center mx-auto mb-8 border border-white/10">
                    <i class="fas fa-inbox text-4xl text-gray-500"></i>
                </div>
                <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-4">No Sentiment Captured</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest max-w-sm mx-auto leading-relaxed">
                    Data ingestion protocols are active but no responses have been processed for this event yet.
                </p>
            </div>
            @endif

            <!-- Footer Action -->
            <div class="mt-20 text-center animate-fade-in" style="animation-delay: 0.4s">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] mb-8">
                    SaaS Feedback Management Engine <span class="text-indigo-400">v3.0</span>
                </p>
                <div class="flex items-center justify-center gap-4">
                    <a href="{{ route('events.show', $event->slug) }}" class="px-8 py-4 bg-white/5 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-white/10 transition-all border border-white/10">
                        <i class="fas fa-arrow-left mr-2"></i> Event Portal
                    </a>
                    <button onclick="window.print()" class="px-8 py-4 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100/20">
                        <i class="fas fa-file-export mr-2"></i> Export Analysis
                    </button>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
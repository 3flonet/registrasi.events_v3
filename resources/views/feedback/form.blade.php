<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | {{ $event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Interactive Star Rating */
        .rating { display: flex; flex-direction: row-reverse; gap: 0.5rem; }
        .rating input { display: none; }
        .rating label { cursor: pointer; transition: all 0.3s ease; color: #e2e8f0; font-size: 2rem; }
        .rating label:hover, .rating label:hover ~ label, .rating input:checked ~ label { color: #f59e0b; transform: scale(1.1); }
        .rating label:active { transform: scale(0.9); }
    </style>
</head>
<body class="bg-[#1a1235] min-h-screen relative overflow-x-hidden selection:bg-indigo-500 selection:text-white">
    
    <!-- Background Decor -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-[10%] -right-[10%] w-[50%] h-[50%] bg-indigo-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -left-[10%] w-[50%] h-[50%] bg-purple-600/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="container mx-auto px-4 py-12 md:py-24">
        <div class="max-w-2xl mx-auto">
            
            <!-- Event OG Branding -->
            @if($event->hasMedia('og_image'))
            <div class="w-full h-40 md:h-56 rounded-[2.5rem] overflow-hidden mb-12 shadow-2xl shadow-indigo-900/20 border border-white/10 animate-fade-in relative group">
                <img src="{{ $event->getFirstMediaUrl('og_image') }}" alt="{{ $event->name }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-[#1a1235]/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-6 left-8">
                    <p class="text-[9px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-1">Event Branding</p>
                    <h2 class="text-xl font-black text-white uppercase tracking-tighter leading-none">{{ $event->name }}</h2>
                </div>
            </div>
            @endif

            <!-- Event Brand -->
            <div class="text-center mb-12 animate-fade-in">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 backdrop-blur-xl rounded-full border border-white/10 mb-6">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-white uppercase tracking-[0.3em]">Participant Voice</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tighter mb-4 leading-none">
                    Feedback <span class="text-indigo-400">Hub</span>
                </h1>
                <p class="text-gray-400 font-medium text-sm md:text-base uppercase tracking-widest">{{ $event->name }}</p>
            </div>

            <!-- Form Card -->
            <div class="glass-card rounded-4xl p-8 md:p-12 shadow-2xl animate-fade-in shadow-indigo-900/20">
                <form action="{{ route('feedback.store', ['event' => $event->slug, 'registration' => $registration->uuid]) }}" method="POST" class="space-y-10">
                    @csrf
                    
                    <div class="space-y-8">
                        @foreach($formFields as $field)
                        @if($field['type'] === 'section')
                            <div class="pt-10 pb-2 border-b border-gray-100 mb-4 first:pt-0">
                                <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $field['label'] }}</h3>
                                @if(!empty($field['description']))
                                    <p class="text-gray-400 text-sm font-medium mt-1">{{ $field['description'] }}</p>
                                @endif
                            </div>
                        @else
                        <div class="space-y-3 group">
                            <label class="block text-[11px] font-black text-[#1a1235] uppercase tracking-widest ml-1">
                                {{ $field['label'] }}
                                @if(!empty($field['required'])) <span class="text-indigo-500">*</span> @endif
                            </label>

                            <div class="relative">
                                @if($field['type'] === 'textarea')
                                    <textarea name="{{ $field['name'] }}" rows="4" 
                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none placeholder-gray-300"
                                        placeholder="Type your thoughts here..."
                                        @if(!empty($field['required'])) required @endif>{{ old($field['name']) }}</textarea>

                                @elseif($field['type'] === 'select')
                                    <div class="relative">
                                        <select name="{{ $field['name'] }}" 
                                            class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-black text-[#1a1235] uppercase tracking-widest focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none appearance-none cursor-pointer"
                                            @if(!empty($field['required'])) required @endif>
                                            <option value="">Select Option</option>
                                            @if(!empty($field['options']) && is_array($field['options']))
                                                @foreach($field['options'] as $option)
                                                    <option value="{{ $option }}" {{ old($field['name']) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </div>

                                @elseif($field['type'] === 'rating')
                                    <div class="rating py-2">
                                        @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="star-{{ $field['name'] }}-{{ $i }}" name="{{ $field['name'] }}" value="{{ $i }}" {{ old($field['name']) == $i ? 'checked' : '' }} @if(!empty($field['required'])) required @endif>
                                        <label for="star-{{ $field['name'] }}-{{ $i }}">
                                            <i class="fas fa-star"></i>
                                        </label>
                                        @endfor
                                    </div>

                                @elseif($field['type'] === 'radio')
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @if(!empty($field['options']) && is_array($field['options']))
                                            @foreach($field['options'] as $option)
                                            <label class="relative flex items-center justify-center p-4 bg-gray-50 border-2 border-transparent rounded-2xl cursor-pointer hover:bg-white hover:border-indigo-100 transition-all has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500 has-[:checked]:ring-4 has-[:checked]:ring-indigo-500/10">
                                                <input type="radio" name="{{ $field['name'] }}" value="{{ $option }}" class="sr-only" {{ old($field['name']) == $option ? 'checked' : '' }} @if(!empty($field['required'])) required @endif>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-500 group-has-[:checked]:text-indigo-600">{{ $option }}</span>
                                            </label>
                                            @endforeach
                                        @endif
                                    </div>

                                @else
                                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ old($field['name']) }}"
                                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl text-sm font-medium text-[#1a1235] focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none placeholder-gray-300"
                                        placeholder="Enter response..."
                                        @if(!empty($field['required'])) required @endif>
                                @endif
                            </div>
                            @error($field['name']) 
                                <p class="text-rose-500 text-[10px] font-black uppercase tracking-widest mt-2 ml-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </p> 
                            @enderror
                        </div>
                        @endif
                        @endforeach
                    </div>

                    <!-- Footer Action -->
                    <div class="pt-8 border-t border-gray-100">
                        <button type="submit" class="w-full py-6 bg-indigo-600 text-white rounded-[2rem] text-sm font-black uppercase tracking-[0.3em] hover:bg-indigo-700 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-2xl shadow-indigo-200">
                            Submit Feedback <i class="fas fa-paper-plane ml-3"></i>
                        </button>
                        <p class="text-center text-[9px] font-bold text-gray-400 mt-6 uppercase tracking-widest">
                            Your identity is securely authenticated via registration protocols.
                        </p>
                    </div>
                </form>
            </div>

            <!-- Global Footer -->
            <div class="mt-12 text-center animate-fade-in" style="animation-delay: 0.3s">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.2em]">
                    Powered by <span class="text-indigo-400">Registrasi.Events</span> Ecosystem
                </p>
            </div>

        </div>
    </div>

</body>
</html>
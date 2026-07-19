<div class="min-h-screen bg-[#FDFDFE] font-outfit pb-24">

    {{-- DYNAMIC ARCHITECT HEADER --}}
    <div class="relative h-[60vh] md:h-[50vh] bg-[#1a1235] overflow-hidden flex items-center justify-center">
        @if($form->getFirstMediaUrl('thumbnail'))
             <div class="absolute inset-0">
                <img src="{{ $form->getFirstMediaUrl('thumbnail') }}" class="w-full h-full object-cover opacity-40 grayscale group-hover:grayscale-0 transition-all duration-1000" alt="Header Background">
                <div class="absolute inset-0 bg-gradient-to-t from-[#FDFDFE] via-transparent to-transparent"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-[#1a1235] to-indigo-900">
                <div class="absolute inset-0 opacity-10" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>
            </div>
        @endif
        
        <div class="relative z-10 text-center px-6">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-8 h-[1px] bg-white/30"></div>
                <span class="text-[10px] font-black text-white/50 uppercase tracking-[0.5em]">System Registration</span>
                <div class="w-8 h-[1px] bg-white/30"></div>
            </div>
            <h1 class="text-5xl md:text-7xl font-[900] text-white uppercase tracking-tighter leading-none animate-fade-in-up">
                {{ $form->name }}
            </h1>
        </div>
    </div>

    {{-- MAIN CONTENT CARD --}}
    <main class="-mt-48 max-w-6xl mx-auto px-6 relative z-10">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-indigo-500/10 overflow-hidden flex flex-col border border-gray-100 transition-all duration-700">
            
            {{-- MINIMALIST ARCHITECT STEPPER --}}
            @if($step > 1)
            <div class="bg-white border-b border-gray-50 py-10 px-10">
                <div class="flex items-center justify-between max-w-3xl mx-auto relative">
                    {{-- Progress Line Background --}}
                    <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-[1px] bg-gray-100"></div>
                    
                    @php
                        $steps = [];
                        $steps[] = ['id' => 2, 'label' => 'Agenda'];
                        if($form->has_categories) $steps[] = ['id' => 3, 'label' => 'Package'];
                        $steps[] = ['id' => 4, 'label' => 'Configuration'];
                    @endphp

                    @foreach($steps as $idx => $s)
                        <div class="relative z-10 flex flex-col items-center group">
                            <div class="w-4 h-4 rounded-full transition-all duration-700 border-2 {{ $step >= $s['id'] ? 'bg-indigo-600 border-indigo-600 scale-125 shadow-lg shadow-indigo-500/20' : 'bg-white border-gray-200' }}"></div>
                            <span class="absolute top-8 text-[9px] font-black uppercase tracking-[0.2em] whitespace-nowrap {{ $step >= $s['id'] ? 'text-indigo-600' : 'text-gray-300' }}">
                                {{ $s['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="p-8 md:p-20">
                
                {{-- STEP 1: WELCOME & INTRO --}}
                @if($step == 1)
                    <div class="animate-fade-in-up flex flex-col items-center text-center">
                        <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center mb-10 rotate-3">
                            <i class="fas fa-paper-plane text-2xl text-indigo-600"></i>
                        </div>
                        
                        <div class="max-w-3xl space-y-8 mb-16">
                            <h2 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em]">Strategic Inquiry Process</h2>
                            <div class="text-gray-400 text-lg md:text-xl font-medium leading-relaxed italic border-l-4 border-indigo-50 pl-8 text-left py-4">
                                {{ $form->getTranslation('description', app()->getLocale()) ?: 'Join our ecosystem as a strategic partner and grow alongside industry leaders.' }}
                            </div>

                            @if($agendas->count() === 1 && $agendas->first()->link_url)
                                <div class="flex justify-center pt-8">
                                    <a href="{{ $agendas->first()->link_url }}" target="_blank" class="px-8 py-3 border border-gray-100 rounded-2xl text-[10px] font-black text-[#1a1235] uppercase tracking-widest hover:bg-[#1a1235] hover:text-white transition-all">
                                        View Event Documentation <i class="fas fa-external-link-alt ml-2 text-[8px]"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-6 w-full justify-center">
                            <button wire:click="startInquiry" class="px-12 py-6 bg-[#1a1235] text-white font-black text-[11px] uppercase tracking-[0.3em] rounded-2xl shadow-2xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all active:scale-95 group leading-none">
                                Begin Inquiry <i class="fas fa-chevron-right ml-3 text-[8px] group-hover:translate-x-1 transition-transform"></i>
                            </button>
                            
                            @if(url()->previous() && url()->previous() != url()->current())
                                <a href="{{ url('/inquiry') }}" class="px-12 py-6 bg-white border border-gray-100 text-[#1a1235] font-black text-[11px] uppercase tracking-[0.3em] rounded-2xl hover:bg-gray-50 transition-all leading-none">
                                    Return to List
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- STEP 2: AGENDA SELECTION --}}
                @if($step == 2)
                    <div class="animate-fade-in-up">
                        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
                            <div>
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] block mb-2">Phase One</span>
                                <h2 class="text-4xl font-[900] text-[#1a1235] uppercase tracking-tighter">Select Event Agenda</h2>
                            </div>
                            <button wire:click="backStep" class="text-[9px] font-black text-gray-400 uppercase tracking-widest hover:text-[#1a1235] transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Discard Selection
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach($agendas as $agenda)
                                <button wire:click="selectAgenda({{ $agenda->id }})" class="group relative bg-white border border-gray-100 rounded-[2rem] p-8 hover:border-[#1a1235] hover:shadow-2xl transition-all duration-700 text-left flex items-start gap-8">
                                    <div class="shrink-0">
                                        @if($agenda->getFirstMediaUrl('default', 'card-banner'))
                                            <img src="{{ $agenda->getFirstMediaUrl('default', 'card-banner') }}" class="h-24 w-24 rounded-2xl object-cover grayscale group-hover:grayscale-0 transition-all duration-700">
                                        @else
                                            <div class="h-24 w-24 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-400 font-black text-3xl group-hover:bg-[#1a1235] group-hover:text-white transition-all">
                                                {{ substr($agenda->title, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0 pt-2">
                                        <h4 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter mb-2 group-hover:text-indigo-600 transition-colors leading-tight">{{ $agenda->title }}</h4>
                                        <div class="flex items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            <i class="far fa-calendar-alt mr-2"></i>
                                            {{ $agenda->start_time ? $agenda->start_time->format('d M Y • H:i') : 'Date TBD' }}
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                            
                            <button wire:click="selectAgenda(null)" class="group bg-gray-50/50 border-2 border-dashed border-gray-100 rounded-[2rem] p-8 hover:border-indigo-200 hover:bg-white transition-all flex items-center gap-8">
                                <div class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center text-gray-300 group-hover:text-indigo-400 transition-all shadow-sm">
                                    <i class="fas fa-globe text-3xl"></i>
                                </div>
                                <div class="text-left">
                                     <span class="block text-xl font-black text-gray-300 uppercase tracking-tighter group-hover:text-[#1a1235] transition-colors">General Inquiry</span>
                                     <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest mt-1 block">Global Partnership Channel</span>
                                </div>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- STEP 3: CATEGORY / PACKAGE SELECTION --}}
                @if($step == 3)
                    <div class="animate-fade-in-up">
                        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16 text-center md:text-left">
                            <div>
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] block mb-2">Phase Two</span>
                                <h2 class="text-4xl font-[900] text-[#1a1235] uppercase tracking-tighter text-center md:text-left">Choose Your Package</h2>
                            </div>
                            <button wire:click="backStep" class="text-[9px] font-black text-gray-400 uppercase tracking-widest hover:text-[#1a1235] transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i> Refine Agenda
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($categories as $category)
                                <div wire:click="selectCategory({{ $category->id }})" class="group bg-white border border-gray-50 rounded-[2.5rem] p-10 hover:shadow-2xl hover:shadow-indigo-500/10 hover:border-indigo-600 transition-all duration-700 cursor-pointer relative overflow-hidden flex flex-col items-center text-center">
                                    <div class="w-12 h-1 bg-gray-100 group-hover:bg-indigo-600 transition-all mb-8"></div>
                                    
                                    <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter mb-4 group-hover:text-indigo-600 transition-colors">{{ $category->name }}</h3>
                                    
                                    @if($category->price)
                                        <div class="text-3xl font-[900] text-[#1a1235] mb-8 py-3 px-6 bg-gray-50 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all tracking-tighter">
                                            <span class="text-xs font-bold text-gray-400 group-hover:text-indigo-200 transition-all mr-1 uppercase">IDR</span>{{ number_format($category->price, 0, ',', '.') }}
                                        </div>
                                    @endif
                                    
                                    <div class="text-gray-400 text-sm leading-relaxed mb-10 font-medium line-clamp-4">
                                        {{ $category->getTranslation('description', app()->getLocale()) ?: 'Premium collaboration experience with exclusive benefits and visibility.' }}
                                    </div>
                                    
                                    <div class="mt-auto w-full">
                                        <button class="w-full py-5 bg-gray-50 text-[#1a1235] rounded-2xl font-black text-[10px] uppercase tracking-widest group-hover:bg-[#1a1235] group-hover:text-white transition-all">
                                            Select Package
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- STEP 4: DATA CONFIGURATION (FIELDS) --}}
                @if($step == 4)
                    <div class="animate-fade-in-up max-w-3xl mx-auto">
                        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16 pb-8 border-b border-gray-50">
                             <div>
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.4em] block mb-2">Final Phase</span>
                                <h2 class="text-4xl font-[900] text-[#1a1235] uppercase tracking-tighter">Configuration</h2>
                             </div>
                             <div class="flex flex-wrap gap-2 justify-center">
                                @if($agendaModel)
                                    <span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[8px] font-black uppercase tracking-widest">{{ Str::limit($agendaModel->title, 20) }}</span>
                                @endif
                                @if($selectedCategoryModel)
                                    <span class="px-4 py-2 bg-[#1a1235] text-white rounded-xl text-[8px] font-black uppercase tracking-widest ml-1">{{ $selectedCategoryModel->name }}</span>
                                @endif
                             </div>
                        </div>

                         <div class="w-full">
                            <form wire:submit.prevent="submit" class="space-y-12">
                                @forelse($form->fields as $field)
                                    <div class="space-y-4">
                                        @if(in_array($field['type'], ['heading', 'paragraph']))
                                           <div class="{{ $field['type'] === 'heading' ? 'pt-8' : '' }}">
                                                @if($field['type'] === 'heading')
                                                    <h4 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">{{ $field['label'] }}</h4>
                                                    <div class="w-12 h-1 bg-indigo-600 mt-2"></div>
                                                @else
                                                    <p class="text-gray-400 text-sm italic font-medium leading-relaxed">{{ $field['label'] }}</p>
                                                @endif
                                           </div>
                                        @else
                                            <div class="group">
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-indigo-600 transition-colors">
                                                    {{ $field['label'] }} @if(isset($field['required']) && $field['required']) <span class="text-red-500">*</span> @endif
                                                </label>

                                                @if($field['type'] === 'textarea')
                                                    <textarea wire:model="formData.{{ $field['name'] }}" rows="5" class="block w-full px-8 py-6 bg-gray-50 border-none rounded-[2rem] text-[#1a1235] font-bold text-sm focus:ring-2 focus:ring-indigo-600 transition-all placeholder-gray-300 shadow-inner group-hover:bg-white transition-colors"></textarea>
                                                @elseif($field['type'] === 'select')
                                                     <div class="relative">
                                                         <select wire:model="formData.{{ $field['name'] }}" class="block w-full px-8 py-6 bg-gray-50 border-none rounded-2xl text-[#1a1235] font-black uppercase tracking-widest text-[9px] focus:ring-2 focus:ring-indigo-600 transition-all appearance-none cursor-pointer">
                                                             <option value="">Choose Option</option>
                                                             @php
                                                                 $opts = $field['options'] ?? '';
                                                                 if (!is_array($opts)) {
                                                                     $opts = explode(',', $opts);
                                                                 }
                                                             @endphp
                                                             @foreach($opts as $opt)
                                                                 <option value="{{ trim($opt) }}">{{ trim($opt) }}</option>
                                                             @endforeach
                                                         </select>
                                                        <i class="fas fa-chevron-down absolute right-8 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none"></i>
                                                     </div>
                                                @elseif(in_array($field['type'], ['file', 'image', 'signature']))
                                                    <div class="relative group/file">
                                                        <div class="flex flex-col items-center justify-center px-10 py-12 border-2 border-dashed border-gray-100 rounded-[2.5rem] bg-gray-50 group-hover/file:border-indigo-400 group-hover/file:bg-indigo-50 transition-all cursor-pointer relative overflow-hidden">
                                                            @if(isset($tempFiles[$field['name']]))
                                                                <div class="text-center z-10">
                                                                     <div class="w-16 h-16 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 animate-bounce">
                                                                        <i class="fas fa-check"></i>
                                                                     </div>
                                                                     <span class="block text-[10px] font-black text-[#1a1235] uppercase tracking-widest">{{ $tempFiles[$field['name']]->getClientOriginalName() }}</span>
                                                                     <span class="block text-[8px] font-bold text-emerald-500 uppercase tracking-widest mt-1 italic">Ready for validation</span>
                                                                </div>
                                                            @else
                                                                <div class="text-center z-10">
                                                                     <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm text-gray-300 group-hover/file:text-indigo-600 transition-all">
                                                                        <i class="fas {{ $field['type'] === 'signature' ? 'fa-signature' : 'fa-cloud-upload-alt' }} text-2xl"></i>
                                                                     </div>
                                                                     <span class="block text-[10px] font-black text-[#1a1235] uppercase tracking-[0.2em] mb-2">Binary Upload Module</span>
                                                                     <span class="block text-[8px] font-black text-gray-300 uppercase tracking-[0.2em]">MAX SIZE: 5MB • PDF, JPG, PNG</span>
                                                                </div>
                                                            @endif
                                                            <input type="file" wire:model="tempFiles.{{ $field['name'] }}" class="absolute inset-0 opacity-0 cursor-pointer">
                                                        </div>
                                                        <div wire:loading wire:target="tempFiles.{{ $field['name'] }}" class="absolute inset-0 bg-white/80 backdrop-blur-md flex items-center justify-center rounded-[2.5rem] z-20">
                                                            <div class="flex items-center gap-3">
                                                                <i class="fas fa-spinner fa-spin text-indigo-600"></i>
                                                                <span class="text-[9px] font-black uppercase text-indigo-600 tracking-widest">Integrating Binary...</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <input type="{{ $field['type'] ?? 'text' }}" wire:model="formData.{{ $field['name'] }}" class="block w-full px-8 py-6 bg-gray-50 border-none rounded-2xl text-[#1a1235] font-bold text-sm focus:ring-2 focus:ring-indigo-600 transition-all shadow-inner group-hover:bg-white placeholder-gray-300" placeholder="Identity Input Engine">
                                                @endif
                                                
                                                @error('formData.' . $field['name']) <span class="text-red-500 text-[9px] font-black uppercase tracking-widest mt-3 block ml-4 italic">{{ $message }}</span> @enderror
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="p-10 bg-yellow-50 text-[#1a1235] rounded-[2rem] border-2 border-dashed border-yellow-200 text-center">
                                        <span class="text-[10px] font-black uppercase tracking-widest block mb-1">Warning: Logical Gap</span>
                                        <span class="text-xs font-bold italic">No interactive fields configured for this blueprint.</span>
                                    </div>
                                @endforelse

                                <div class="pt-16 flex flex-col items-center">
                                    <div class="flex gap-4 w-full">
                                        <button type="button" wire:click="backStep" class="w-20 h-[72px] bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center hover:bg-[#1a1235] hover:text-white transition-all">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                        <button type="submit" wire:loading.attr="disabled" class="flex-1 py-6 px-10 bg-[#1a1235] text-white font-black text-[12px] uppercase tracking-[0.4em] rounded-2xl shadow-2xl shadow-indigo-500/20 hover:bg-indigo-700 transition-all leading-none group flex items-center justify-center gap-4 active:scale-[0.98]">
                                            <span wire:loading.remove wire:target="submit" class="flex items-center gap-3">
                                                Finalize Authorization <i class="fas fa-chevron-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                                            </span>
                                            <span wire:loading wire:target="submit" class="flex items-center gap-3">
                                                <i class="fas fa-spinner fa-spin"></i> Processing Request...
                                            </span>
                                        </button>
                                    </div>
                                    <p class="text-center text-[8px] font-black text-gray-300 uppercase tracking-[0.2em] mt-8 leading-relaxed max-w-sm">
                                        By authorizing this inquiry, you agree to our encrypted data policy and system architecture standards.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </main>

    <style>
        @keyframes fadeInUps {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUps 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        input:focus, textarea:focus, select:focus {
            box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.2) !important;
        }
    </style>
</div>

@push('styles')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Premium Textarea Styling */
    .editor-textarea {
        background: #ffffff;
        border: 2px solid #f1f5f9;
        border-radius: 24px;
        padding: 2rem;
        font-family: 'Fira Code', monospace;
        font-size: 14px;
        line-height: 1.7;
        color: #1a1235;
        transition: all 0.3s ease;
        resize: vertical;
    }
    .editor-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 8px rgba(59, 130, 246, 0.05);
        background: #fff;
    }
</style>
@endpush

<div class="max-w-none mx-auto pb-12 font-sans" x-data="templateManager()">
    {{-- Header Section --}}
    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 mb-10 overflow-hidden relative group animate-fade-in">
        <div class="absolute top-0 right-0 p-10 opacity-[0.03] -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-1000">
            <i class="fas fa-magic text-[200px] rotate-12"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-8">
                <div class="w-20 h-20 bg-gradient-to-br from-teal-50 to-emerald-50 text-teal-600 rounded-3xl flex items-center justify-center text-3xl shadow-inner group-hover:rotate-6 transition-transform">
                    <i class="fas {{ $templateId ? 'fa-pen-nib' : 'fa-plus-circle' }}"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-teal-50 text-teal-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Automation Hub</span>
                        <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Message Templates</span>
                    </div>
                    <h1 class="text-4xl font-[950] text-[#1a1235] uppercase tracking-tighter leading-none">
                        @if($templateId) Edit <span class="text-teal-600">Template</span> @else New <span class="text-teal-600">Template</span> @endif
                    </h1>
                </div>
            </div>
            <a href="{{ route('admin.message-templates.index') }}" wire:navigate class="px-8 py-5 bg-gray-50 text-gray-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-[#1a1235] hover:text-white transition-all shadow-sm border border-gray-100 leading-none flex items-center gap-3 group/btn">
                <i class="fas fa-arrow-left group-hover/btn:-translate-x-1 transition-transform"></i> Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start animate-fade-in">
        
        {{-- LEFT COLUMN: The Inputs --}}
        <div class="lg:col-span-8 space-y-8">
            
            {{-- Template Identity Card --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md animate-fade-in">
                <div class="p-10 space-y-10">
                    {{-- Subject --}}
                    <div class="space-y-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Template Subject / Internal Name</label>
                        <input type="text" x-model="subject" @focus="lastFocused = 'subject'" placeholder="e.g., Early Bird Registration Confirmation" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-base font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-teal-100 transition-all placeholder:text-gray-300">
                        @error('subject') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    {{-- Category & Type --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Communication Category</label>
                            <select wire:model.live="category" x-model="category" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-teal-100 transition-all">
                                <option value="transactional">Transactional (E-Ticket, Payment)</option>
                                <option value="marketing">Marketing & Promotion</option>
                                <option value="informative">Informative / Announcement</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Template Type</label>
                            <div class="flex p-1.5 bg-gray-50 rounded-2xl border border-gray-100">
                                <button type="button" wire:click="$set('type', 'email')" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $type === 'email' ? 'bg-[#1a1235] text-white shadow-lg' : 'text-gray-400 hover:text-gray-600' }}">Email</button>
                                <button type="button" wire:click="$set('type', 'whatsapp')" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $type === 'whatsapp' ? 'bg-emerald-500 text-white shadow-lg' : 'text-gray-400 hover:text-gray-600' }}">WhatsApp</button>
                                <button type="button" wire:click="$set('type', 'both')" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $type === 'both' ? 'bg-indigo-500 text-white shadow-lg' : 'text-gray-400 hover:text-gray-600' }}">Both</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- WhatsApp Template Selection & Preview --}}
            <div x-show="type === 'whatsapp' || type === 'both'" x-cloak class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md animate-fade-in">
                <div class="px-10 py-8 border-b border-gray-50 bg-emerald-50/20 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 shadow-sm"><i class="fab fa-whatsapp text-xl"></i></div>
                        <h3 class="text-[11px] font-black text-emerald-800 uppercase tracking-[0.2em]">Official Meta WhatsApp Template</h3>
                    </div>
                </div>
                <div class="p-10 space-y-6">
                    <div class="space-y-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Select Meta Template</label>
                        <select wire:model.live="whatsapp_template_id" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-teal-100 transition-all">
                            <option value="">-- Choose WhatsApp Template (Approved by Meta) --</option>
                            @foreach($whatsappTemplates as $wt)
                                <option value="{{ $wt->id }}">{{ $wt->name }} [{{ strtoupper($wt->language_code) }}]</option>
                            @endforeach
                        </select>
                        @error('whatsapp_template_id') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    @if($whatsapp_template_id)
                        @php
                            $selectedTemplateForMedia = App\Models\WhatsAppTemplate::find($whatsapp_template_id);
                            $headerType = $selectedTemplateForMedia ? ($selectedTemplateForMedia->parameters['header']['type'] ?? 'none') : 'none';
                        @endphp
                        @if($headerType === 'image' || $headerType === 'video' || $headerType === 'document')
                            <div class="p-6 bg-slate-50 border border-slate-100 rounded-3xl space-y-4 animate-fade-in">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-lg shadow-inner">
                                        @if($headerType === 'image')
                                            <i class="fas fa-image"></i>
                                        @elseif($headerType === 'video')
                                            <i class="fas fa-video"></i>
                                        @else
                                            <i class="fas fa-file-pdf"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-[11px] font-black text-slate-700 uppercase tracking-widest">Custom Event WhatsApp Header Media (Optional)</h4>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">
                                            @if($headerType === 'image')
                                                Upload image override (JPEG/PNG, Max 5MB).
                                            @elseif($headerType === 'video')
                                                Upload video override (MP4, Max 16MB).
                                            @else
                                                Upload PDF document override (PDF, Max 10MB).
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                                    <div class="relative group/upload cursor-pointer">
                                        <input type="file" wire:model="whatsapp_header_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                        <div class="w-full h-32 bg-white border border-slate-200 border-dashed rounded-2xl flex flex-col items-center justify-center gap-2 group-hover/upload:border-teal-400 group-hover/upload:bg-teal-50/20 transition-all">
                                            <i class="fas fa-cloud-upload-alt text-lg text-slate-400 group-hover/upload:text-teal-500"></i>
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Choose custom file</span>
                                        </div>
                                    </div>

                                    <div class="h-32 bg-white border border-slate-200 rounded-2xl flex items-center justify-center overflow-hidden p-2 relative">
                                        @if ($whatsapp_header_file)
                                            @if ($headerType === 'image')
                                                <img src="{{ $whatsapp_header_file->temporaryUrl() }}" class="w-full h-full object-contain rounded-xl">
                                            @elseif ($headerType === 'video')
                                                <div class="text-center font-bold text-[10px] text-teal-600">
                                                    <i class="fas fa-video text-lg block mb-1"></i>
                                                    Video Selected
                                                </div>
                                            @else
                                                <div class="text-center font-bold text-[10px] text-red-500">
                                                    <i class="fas fa-file-pdf text-lg block mb-1"></i>
                                                    PDF Selected
                                                </div>
                                            @endif
                                        @elseif ($existingWhatsappHeaderPath)
                                            @if ($headerType === 'image')
                                                <img src="{{ Storage::url($existingWhatsappHeaderPath) }}" class="w-full h-full object-contain rounded-xl">
                                            @elseif ($headerType === 'video')
                                                <video src="{{ Storage::url($existingWhatsappHeaderPath) }}" class="w-full h-full object-contain rounded-xl" controls></video>
                                            @else
                                                <a href="{{ Storage::url($existingWhatsappHeaderPath) }}" target="_blank" class="text-center font-bold text-[10px] text-red-500 hover:underline">
                                                    <i class="fas fa-file-pdf text-lg block mb-1"></i>
                                                    View Current PDF
                                                </a>
                                            @endif
                                        @else
                                            <div class="text-center text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-relaxed">
                                                <i class="fas fa-times-circle text-base block mb-1 text-slate-200"></i>
                                                Using Default Media<br>from Super Admin
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @error('whatsapp_header_file') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    @endif

                    @if($whatsapp_template_id)
                        @php
                            $selectedTemplate = App\Models\WhatsAppTemplate::find($whatsapp_template_id);
                        @endphp
                        @if($selectedTemplate)
                            {{-- Realtime Preview Bubble --}}
                            <div class="mt-8 p-6 bg-slate-50 rounded-[2rem] border border-gray-100">
                                <h4 class="text-[10px] font-black text-gray-400 mb-6 uppercase tracking-[0.2em] ml-1">WhatsApp Message Preview (Realtime)</h4>
                                <div class="relative mx-auto max-w-[320px] bg-[#efeae2] p-6 rounded-3xl shadow-inner border border-gray-200/50 min-h-[220px] flex flex-col justify-between" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-repeat: repeat;">
                                    <div class="flex-grow">
                                        <div class="bg-white rounded-2xl p-4 text-[12px] shadow-sm max-w-[90%] relative ml-1 mt-1 text-[#111b21] after:content-[''] after:absolute after:top-0 after:left-[-8px] after:w-0 after:h-0 after:border-8 after:border-transparent after:border-r-white after:border-t-white">
                                            
                                            {{-- Header --}}
                                            @if(isset($selectedTemplate->parameters['header']) && $selectedTemplate->parameters['header'])
                                                <div class="mb-3 p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-3 text-[10px] text-slate-500 font-bold">
                                                    @if($selectedTemplate->parameters['header']['type'] === 'document')
                                                        <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                                        <div class="leading-tight">
                                                            <div class="text-[#1a1235] text-[11px]">E-Ticket_Receipt.pdf</div>
                                                            <div class="text-[9px] font-medium text-slate-400 mt-0.5">PDF Document</div>
                                                        </div>
                                                    @elseif($selectedTemplate->parameters['header']['type'] === 'image')
                                                        <i class="fas fa-image text-teal-500 text-2xl"></i>
                                                        <div class="leading-tight">
                                                            <div class="text-[#1a1235] text-[11px]">Ticket_QR_Code.jpg</div>
                                                            <div class="text-[9px] font-medium text-slate-400 mt-0.5">JPEG Image</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Body --}}
                                            <p class="whitespace-pre-line leading-relaxed font-sans">
                                                @php
                                                    $previewText = e($selectedTemplate->body_preview);
                                                    $previewText = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $previewText);
                                                    $previewText = preg_replace('/\_(.*?)\_/', '<em>$1</em>', $previewText);
                                                    
                                                    $mockData = [
                                                        '{{1}}' => 'Budi Santoso',
                                                        '{{2}}' => 'Seminar Teknologi Nasional 2026',
                                                        '{{3}}' => 'REG-12345',
                                                        '{{4}}' => 'Auditorium Utama Lantai 3',
                                                        '{{5}}' => 'Auditorium Utama Lantai 3'
                                                    ];
                                                    $previewText = strtr($previewText, $mockData);
                                                    echo $previewText;
                                                @endphp
                                            </p>
                                            <span class="text-[9px] text-gray-400 float-right mt-2 font-medium">14:30</span>
                                        </div>

                                        {{-- Buttons --}}
                                        @if(isset($selectedTemplate->parameters['buttons']) && !empty($selectedTemplate->parameters['buttons']))
                                            @foreach($selectedTemplate->parameters['buttons'] as $btn)
                                                <div class="mt-1 bg-white hover:bg-slate-50 text-[#00a884] py-3 rounded-2xl text-center text-[12px] font-bold shadow-sm max-w-[90%] ml-1 border-t border-slate-100 cursor-pointer flex items-center justify-center gap-2">
                                                    <i class="fas fa-external-link-alt text-[10px] text-slate-400"></i>
                                                    <span>{{ $btn['value'] === 'ticket_url' ? 'Open E-Ticket QR' : ($btn['value'] === 'payment_link' ? 'Pay Now' : 'Open Link') }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                {{-- Dynamic Buttons Parameter Mapping --}}
                                @php
                                    $dynamicButtons = array_filter($selectedTemplate->parameters['buttons'] ?? [], function($btn) {
                                        return ($btn['type'] ?? '') === 'url' && ($btn['url_type'] ?? '') === 'dynamic';
                                    });
                                @endphp
                                @if(!empty($dynamicButtons))
                                    <div class="mt-8 p-6 bg-white rounded-3xl border border-slate-100">
                                        <h5 class="text-[11px] font-black text-slate-700 mb-4 uppercase tracking-[0.15em] ml-1 flex items-center gap-2">
                                            <i class="fas fa-link text-teal-500"></i> Configure Button Actions (Interactive)
                                        </h5>
                                        <div class="space-y-4">
                                            @foreach($dynamicButtons as $btn)
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center p-4 bg-slate-50 rounded-2xl border border-slate-100/50">
                                                     <div>
                                                         <span class="text-xs font-bold text-[#1a1235] block">{{ $btn['text'] }}</span>
                                                         <span class="text-[10px] text-slate-400 font-medium mt-0.5 block uppercase tracking-wider">Dynamic URL Button</span>
                                                     </div>
                                                     <div class="space-y-2">
                                                         <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Button Link Destination</label>
                                                         <select wire:model="buttons_mapping.{{ $btn['index'] }}" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-[#1a1235] focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all">
                                                             <option value="ticket_url">ticket_url (Tiket Link)</option>
                                                             <option value="payment_link">payment_link (Invoice Link)</option>
                                                         </select>
                                                     </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Test Send Panel --}}
                                <div class="mt-8 p-6 bg-white rounded-3xl border border-slate-100">
                                    <h5 class="text-[11px] font-black text-slate-700 mb-4 uppercase tracking-[0.15em] ml-1 flex items-center gap-2">
                                        <i class="fas fa-vial text-emerald-500"></i> Test Template Dispatch
                                    </h5>
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <input type="text" wire:model="testPhone" placeholder="Phone Number (e.g. 628123456789)" class="flex-grow px-6 py-4 border border-slate-200 rounded-xl text-xs font-bold focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all">
                                        <button type="button" wire:click="sendTestWhatsApp" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 active:scale-[0.98] transition-all">
                                            Send Test Message
                                        </button>
                                    </div>
                                    @error('testPhone') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                                    @if (session()->has('wa_test_success'))
                                        <div class="mt-3 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                            <i class="fas fa-check-circle"></i> {{ session('wa_test_success') }}
                                        </div>
                                    @endif
                                    @if (session()->has('wa_test_error'))
                                        <div class="mt-3 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
                                            <i class="fas fa-exclamation-circle"></i> {{ session('wa_test_error') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Email Editor Card --}}
            <div x-show="type === 'email' || type === 'both'" x-cloak class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md animate-fade-in">
                <div class="px-10 py-8 border-b border-gray-50 bg-indigo-50/20 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm"><i class="fas fa-envelope-open-text"></i></div>
                        <h3 class="text-[11px] font-black text-indigo-800 uppercase tracking-[0.2em]">Official HTML Email Template</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="mode = 'visual'" :class="mode === 'visual' ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-600 border border-indigo-100'" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Editor</button>
                        <button type="button" @click="mode = 'preview'" :class="mode === 'preview' ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-600 border border-indigo-100'" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2"><i class="fas fa-eye"></i> Preview</button>
                    </div>
                </div>
                <div class="p-10 space-y-8">
                    {{-- Email Banner Upload Section --}}
                    <div class="p-6 bg-slate-50 border border-slate-100 rounded-3xl space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-lg shadow-inner">
                                <i class="fas fa-image"></i>
                            </div>
                            <div>
                                <h4 class="text-[11px] font-black text-slate-700 uppercase tracking-widest">Email Header Banner (Optional)</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Recommended: 1200x400px (Max 2MB)</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                            <div class="relative group/upload cursor-pointer">
                                <input type="file" wire:model="banner" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                <div class="w-full h-32 bg-white border border-slate-200 border-dashed rounded-2xl flex flex-col items-center justify-center gap-2 group-hover/upload:border-teal-400 group-hover/upload:bg-teal-50/20 transition-all">
                                    <i class="fas fa-cloud-upload-alt text-lg text-slate-400 group-hover/upload:text-teal-500"></i>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Choose banner image</span>
                                </div>
                            </div>

                            <div class="h-32 bg-white border border-slate-200 rounded-2xl flex items-center justify-center overflow-hidden p-2 relative">
                                @if ($banner)
                                    <img src="{{ $banner->temporaryUrl() }}" class="w-full h-full object-contain rounded-xl">
                                @elseif ($existingBannerPath)
                                    <img src="{{ Storage::url($existingBannerPath) }}" class="w-full h-full object-contain rounded-xl">
                                @else
                                    <div class="text-center text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-relaxed">
                                        <i class="fas fa-eye-slash text-base block mb-1 text-slate-200"></i>
                                        No Banner Selected
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('banner') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email Body --}}
                    <div class="space-y-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Email Body (HTML Professional)</label>
                        <div class="p-0 border-2 border-gray-50 bg-gray-50/50 rounded-3xl overflow-hidden">
                            <div x-show="mode === 'visual'">
                                <textarea x-model="content" x-ref="emailArea" @focus="lastFocused = 'visual'" class="w-full min-h-[500px] p-10 font-mono text-sm bg-white text-[#1a1235] border-none focus:ring-0 no-scrollbar" placeholder="Paste your HTML code here..."></textarea>
                            </div>
                            <div x-show="mode === 'preview'" x-cloak class="bg-gray-100 p-10 min-h-[500px] flex justify-center">
                                <iframe x-ref="previewFrame" class="w-full max-w-[650px] bg-white shadow-2xl rounded-3xl border-none min-h-[600px]"></iframe>
                            </div>
                        </div>
                        @error('content') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Finalize Action --}}
            <button type="submit" class="w-full py-6 bg-teal-600 text-white rounded-[2rem] font-black text-sm uppercase tracking-[0.3em] shadow-xl hover:bg-teal-700 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-4 group relative z-10">
                <i class="fas fa-save group-hover:scale-110 transition-transform"></i>
                Save Template
            </button>
        </div>

        {{-- RIGHT COLUMN: Magic Library & Actions --}}
        <div class="lg:col-span-4 space-y-8 sticky top-8">
            
            {{-- Magic Library Card --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-10 py-8 flex items-center gap-4" style="background: linear-gradient(to right, #14b8a6, #059669);">
                    <i class="fas fa-magic text-white text-sm animate-pulse"></i>
                    <h3 class="text-[11px] font-black text-white uppercase tracking-[0.2em]">Magic Library</h3>
                </div>
                <div class="p-6 space-y-3 max-h-[400px] overflow-y-auto no-scrollbar">
                    <template x-for="(tpl, key) in examples" :key="key">
                        <button type="button" @click="applyTemplate(key)" onmousedown="event.preventDefault()" class="w-full p-5 bg-white border border-gray-100 rounded-3xl hover:border-teal-400 hover:bg-teal-50 transition-all text-left shadow-sm group">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[10px] font-black text-[#1a1235] uppercase group-hover:text-teal-600 transition-colors" x-text="tpl.title"></p>
                                <i class="fas fa-chevron-right text-[8px] text-gray-200 group-hover:text-teal-400 transition-all"></i>
                            </div>
                            <p class="text-[8px] text-gray-400 uppercase tracking-widest leading-none">Instant Template</p>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Dynamic Tags Card --}}
            <div class="bg-[#1a1235] rounded-[2.5rem] shadow-2xl p-10 overflow-hidden relative group">
                <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-teal-600/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <h4 class="text-[11px] font-black uppercase tracking-[0.3em] text-white/50">Dynamic Tags</h4>
                        <span class="px-2 py-1 bg-white/5 rounded text-[8px] font-bold text-white/30 uppercase">Click to Insert</span>
                    </div>
                    <div class="space-y-6 max-h-[500px] overflow-y-auto no-scrollbar pr-2">
                        <template x-for="(group, name) in tags" :key="name">
                            <div class="space-y-3">
                                <p class="text-[8px] font-black uppercase tracking-[0.2em] text-teal-400/60 ml-1" x-text="name"></p>
                                <div class="space-y-2">
                                    <template x-for="tag in group" :key="tag.code">
                                        <button type="button" @click="insertTag(tag.code)" onmousedown="event.preventDefault()" class="w-full flex items-center justify-between p-3.5 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/10 hover:border-teal-400 transition-all group/tag text-left">
                                            <code class="text-teal-300 text-[10px] font-mono font-black italic group-hover/tag:scale-105 transition-transform" x-text="tag.code"></code>
                                            <span class="text-[7px] text-white/40 font-bold uppercase tracking-widest" x-text="tag.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Fonnte Support Note --}}
                    <div class="mt-8 p-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 animate-pulse">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fab fa-whatsapp text-emerald-400 text-[10px]"></i>
                            <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">Fonnte Optimized</span>
                        </div>
                        <p class="text-[8px] text-emerald-200/60 leading-relaxed font-medium italic">
                            Semua dynamic tags di atas (termasuk <span class="text-white font-bold">{ticket_qrcode}</span>) didukung penuh oleh integrasi WhatsApp Fonnte untuk personalisasi pesan otomatis secara real-time dengan dukungan gambar asli.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function templateManager() {
        const TAGS_LIBRARY = {
            "Event Basics": [
                { code: '{banner}', label: 'Email Header Image' },
                { code: '{event_name}', label: 'Event Title' },
                { code: '{organizer}', label: 'Organizer Name' },
                { code: '{date}', label: 'Event Date' },
                { code: '{time}', label: 'Event Start Time' },
                { code: '{checkin_time}', label: 'Actual Check-in Time' }
            ],
            "Smart Placeholders": [
                { code: '{event_type}', label: 'Mode (Physical/Virtual/Hybrid)' },
                { code: '{event_instruction}', label: 'Smart Venue / Access Link' },
                { code: '{name}', label: 'Participant Name' },
                { code: '{jabatan}', label: 'Position / Job Title' },
                { code: '{company}', label: 'Organization / Company' }
            ],
            "Ticketing & Sales": [
                { code: '{ticket_code}', label: 'Unique Ticket ID' },
                { code: '{ticket_qrcode}', label: 'Ticket QR Code (Image)' },
                { code: '{ticket_tier}', label: 'Ticket Category' },
                { code: '{payment_link}', label: 'Payment Invoice URL' },
                { code: '{total_bayar}', label: 'Total Amount' }
            ],
            "Post Event": [
                { code: '{link_certificate}', label: 'Certificate URL' },
                { code: '[link_sertifikat]', label: 'Link Sertifikat' },
                { code: '{link_feedback}', label: 'Feedback URL' }
            ]
        };

        const EXAMPLES_LIBRARY = {
            transactional: {
                title: 'E-Ticket Confirmation',
                subject: '🎟️ Registration Confirmed: {event_name}',
                wa: 'Halo *{name}*! 👋\n\nSelamat! Pendaftaran Anda untuk *{event_name}* telah berhasil dikonfirmasi.\n\n🎫 *TIKET:* {ticket_code}\n📅 *TANGGAL:* {date}\n📍 *LOKASI:* {event_instruction}\n\nSampai jumpa di acara! 🚀',
                email: `<div style='background-color: #f8fafc; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><h1 style='color: #1a1235; font-size: 28px;'>PENDAFTARAN BERHASIL!</h1><p style='color: #64748b;'>Halo {name}, kursi Anda telah diamankan untuk {event_name}.</p><div style='background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 24px; padding: 30px; margin: 30px 0;'><h2 style='margin-top: 15px;'>{ticket_code}</h2></div><p>Lokasi: {event_instruction}</p><a href='{link_ticket}' style='display: inline-block; background: #322365; color: white; padding: 20px 40px; text-decoration: none; border-radius: 15px; font-weight: bold;'>LIHAT E-TIKET</a></div></div></div>`
            },
            auto_checkin: {
                title: 'Check-in Notification',
                subject: '✨ Welcome to {event_name}!',
                wa: 'Selamat Datang, *{name}*! ✨\n\nSenang sekali Anda telah hadir di *{event_name}*. Kehadiran Anda telah tercatat pada pukul *{checkin_time}*.\n\nNikmati acaranya! 🌟',
                email: `<div style='background-color: #ecfdf5; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><h1 style='color: #064e3b; font-size: 28px;'>SELAMAT DATANG!</h1><p style='color: #065f46;'>Halo {name}, Anda telah berhasil check-in di <strong>{event_name}</strong>.</p><div style='background: #f0fdf4; border-radius: 24px; padding: 25px; margin: 30px 0; border: 1px solid #d1fae5;'><p style='margin: 0; font-size: 14px; color: #059669;'>Waktu Kehadiran</p><h2 style='margin: 5px 0 0 0; color: #064e3b;'>{checkin_time}</h2></div><p style='color: #64748b;'>Selamat menikmati rangkaian acara kami!</p></div></div></div>`
            },
            event_invoice: {
                title: 'Invoice Notification',
                subject: '⏳ Pembayaran Tiket: {event_name}',
                wa: 'Halo *{name}*,\n\nTerima kasih telah mendaftar. Harap selesaikan pembayaran agar tiket dapat segera kami terbitkan.\n\n💰 *TOTAL:* {total_bayar}\n🔗 *LINK BAYAR:* {payment_link}',
                email: `<div style='background-color: #f8fafc; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border-top: 10px solid #1e293b;'><div style='padding: 50px;'><p style='color: #64748b; font-weight: 800; font-size: 12px; letter-spacing: 2px; margin-bottom: 10px;'>OFFICIAL INVOICE</p><h1 style='color: #1a1235; font-size: 32px; margin: 0 0 30px 0;'>{event_name}</h1><div style='background: #f8fafc; border-radius: 24px; padding: 30px; margin-bottom: 30px;'><table width='100%' cellpadding='0' cellspacing='0'><tr><td style='color: #64748b; padding-bottom: 15px;'>Nama Peserta</td><td align='right' style='color: #1a1235; font-weight: bold; padding-bottom: 15px;'>{name}</td></tr><tr><td style='color: #64748b; padding-top: 15px; border-top: 1px solid #e2e8f0;'>Total Tagihan</td><td align='right' style='color: #1e293b; font-size: 24px; font-weight: 900; padding-top: 15px; border-top: 1px solid #e2e8f0;'>{total_bayar}</td></tr></table></div><a href='{payment_link}' style='display: block; background: #1e293b; color: white; text-align: center; padding: 22px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);'>BAYAR SEKARANG</a><p style='text-align: center; color: #94a3b8; font-size: 12px; margin-top: 30px;'>Harap segera selesaikan pembayaran untuk mengamankan slot Anda.</p></div></div></div>`
            },
            reminder: {
                title: 'Event Reminder',
                subject: '⏰ PENGINGAT: {event_name} Segera Dimulai!',
                wa: 'Halo *{name}*! 👋\n\nIni adalah pengingat bahwa event *{event_name}* akan segera dimulai.\n\n📅 *TANGGAL:* {date}\n⏰ *JAM:* {time}\n📍 *LOKASI:* {event_instruction}\n\nSampai jumpa! 🚀',
                email: `<div style='background-color: #fffbeb; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><h1 style='color: #92400e; font-size: 28px;'>SUDAH SIAP?</h1><p style='color: #b45309;'>Halo {name}, kami sudah tidak sabar bertemu Anda di <strong>{event_name}</strong>.</p><div style='background: #fffbeb; border-radius: 24px; padding: 30px; margin: 30px 0; text-align: left;'><div style='margin-bottom: 15px;'><small style='color: #b45309; font-weight: bold;'>WAKTU & TEMPAT</small><div style='color: #92400e; font-size: 18px; font-weight: bold; margin-top: 5px;'>{date} • {time}</div></div><div style='color: #92400e; font-size: 14px; line-height: 1.6;'>{event_instruction}</div></div><a href='{link_ticket}' style='display: inline-block; background: #f59e0b; color: white; padding: 20px 40px; text-decoration: none; border-radius: 15px; font-weight: bold;'>SIAPKAN E-TIKET</a></div></div></div>`
            },
            certificate: {
                title: 'Certificate Notification',
                subject: '🎓 E-Certificate: {event_name}',
                wa: 'Halo *{name}*! 👋\n\nSelamat! Sertifikat resmi Anda untuk *{event_name}* telah siap.\n\n🔗 *DOWNLOAD:* {link_certificate}',
                email: `<div style='background-color: #f0fdf4; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><h1 style='color: #064e3b; font-size: 28px;'>APRESIASI UNTUK ANDA!</h1><p style='color: #065f46;'>Halo {name}, terima kasih telah berpartisipasi di {event_name}. Sertifikat resmi Anda telah terbit.</p><div style='margin: 40px 0;'><a href='{link_certificate}' style='display: inline-block; background: #10b981; color: white; padding: 22px 45px; text-decoration: none; border-radius: 20px; font-weight: bold; font-size: 16px;'>UNDUH SERTIFIKAT (PDF)</a></div><p style='color: #94a3b8; font-size: 12px;'>Sertifikat ini merupakan bentuk penghargaan atas kehadiran dan kontribusi Anda.</p></div></div></div>`
            },
            event_feedback: {
                title: 'Feedback Survey',
                subject: '🙏 Thank You! How was {event_name}?',
                wa: 'Halo *{name}*! 👋\n\nTerima kasih telah hadir di *{event_name}*. Kami ingin mendengar pendapat Anda.\n\n🔗 *SURVEY:* {link_feedback}',
                email: `<div style='background-color: #fdf2f8; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><h1 style='color: #831843; font-size: 28px;'>KAMI INGIN MENDENGAR ANDA</h1><p style='color: #9d174d;'>Halo {name}, bagaimana kesan Anda mengikuti <strong>{event_name}</strong>?</p><p style='color: #64748b; margin-bottom: 30px;'>Bantu kami menjadi lebih baik dengan memberikan feedback singkat.</p><a href='{link_feedback}' style='display: inline-block; background: #db2777; color: white; padding: 22px 45px; text-decoration: none; border-radius: 20px; font-weight: bold;'>ISI SURVEY SINGKAT</a></div></div></div>`
            },
            simple_pro: {
                title: 'Simple Professional',
                subject: 'Notification: {event_name}',
                wa: 'Halo {name},\n\n(Isi pesan Anda di sini)\n\nTerima kasih.',
                email: `<div style='background-color: #f8fafc; padding: 40px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 24px; border: 1px solid #e2e8f0; padding: 40px;'><div style='line-height: 1.6; color: #1a1235; font-size: 16px;'>{content}</div></div></div>`
            },
            event_cancellation: {
                title: 'Cancellation Notice',
                subject: '📢 Penting: Pembatalan Acara {event_name}',
                wa: 'Halo *{name}*,\n\nKami menyesal menginformasikan bahwa event *{event_name}* terpaksa dibatalkan.\n\nMohon maaf atas ketidaknyamanannya.',
                email: `<div style='background-color: #fef2f2; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><h1 style='color: #991b1b; font-size: 28px;'>PENGUMUMAN PENTING</h1><p style='color: #b91c1c;'>Halo {name}, kami memohon maaf karena <strong>{event_name}</strong> terpaksa dibatalkan.</p></div></div></div>`
            }
        };

        return {
            category: @entangle('category'),
            subject: @entangle('subject'),
            whatsapp_content: @entangle('whatsapp_content'),
            content: @entangle('content'),
            type: @entangle('type'),
            lastFocused: 'visual',
            mode: 'visual',
            tags: TAGS_LIBRARY,
            examples: EXAMPLES_LIBRARY,
            init() {
                this.$watch('mode', value => {
                    if (value === 'preview') {
                        const frame = this.$refs.previewFrame;
                        const doc = frame.contentDocument || frame.contentWindow.document;
                        doc.open();
                        doc.write(`
                            <!DOCTYPE html>
                            <html>
                                <head>
                                    <style>body { margin: 0; padding: 0; }</style>
                                </head>
                                <body>${this.content}</body>
                            </html>
                        `);
                        doc.close();
                    }
                });
            },
            applyTemplate(key) {
                const tpl = this.examples[key];
                if (tpl) {
                    this.subject = tpl.subject;
                    this.whatsapp_content = tpl.wa;
                    this.content = tpl.email;
                    this.mode = 'preview';
                    
                    @this.set('content', this.content, false);
                    @this.set('subject', this.subject, false);
                    @this.set('whatsapp_content', this.whatsapp_content, false);
                    Swal.fire({ icon: 'success', title: 'Applied!', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                }
            },
            insertTag(tag) {
                if (this.lastFocused === 'visual') {
                    const el = this.$refs.emailArea;
                    if (el) {
                        const start = el.selectionStart;
                        const end = el.selectionEnd;
                        this.content = this.content.substring(0, start) + tag + this.content.substring(end);
                        el.focus();
                        setTimeout(() => { el.selectionStart = el.selectionEnd = start + tag.length; }, 10);
                    }
                } else if (this.lastFocused === 'whatsapp') {
                    const el = document.getElementById('whatsapp-textarea');
                    if (el) {
                        const start = el.selectionStart;
                        const end = el.selectionEnd;
                        this.whatsapp_content = this.whatsapp_content.substring(0, start) + tag + this.whatsapp_content.substring(end);
                        el.focus();
                        setTimeout(() => { el.selectionStart = el.selectionEnd = start + tag.length; }, 10);
                    }
                } else if (this.lastFocused === 'subject') {
                    const el = document.querySelector('input[x-model="subject"]');
                    if (el) {
                        const start = el.selectionStart;
                        const end = el.selectionEnd;
                        this.subject = this.subject.substring(0, start) + tag + this.subject.substring(end);
                        el.focus();
                        setTimeout(() => { el.selectionStart = el.selectionEnd = start + tag.length; }, 10);
                    }
                }
            },
            applyStyle(symbol) {
                const textarea = document.querySelector('textarea[x-model="whatsapp_content"]');
                if (!textarea) return;

                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const selectedText = this.whatsapp_content.substring(start, end);

                if (selectedText) {
                    this.whatsapp_content = this.whatsapp_content.substring(0, start) +
                        symbol + selectedText + symbol +
                        this.whatsapp_content.substring(end);
                } else {
                    this.whatsapp_content = this.whatsapp_content.substring(0, start) +
                        symbol + symbol +
                        this.whatsapp_content.substring(end);
                    
                    setTimeout(() => {
                        textarea.selectionStart = textarea.selectionEnd = start + 1;
                        textarea.focus();
                    }, 10);
                }
            }
        }
    }
</script>
@endpush

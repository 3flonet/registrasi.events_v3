<div class="max-w-none mx-auto pb-12 font-sans">
    {{-- Header Section --}}
    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 mb-10 overflow-hidden relative group">
        <div class="absolute top-0 right-0 p-10 opacity-[0.03] -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-1000">
            <i class="fab fa-whatsapp text-[200px] rotate-12 text-emerald-600"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-8">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-50 to-teal-50 text-emerald-600 rounded-3xl flex items-center justify-center text-3xl shadow-inner">
                    <i class="fas {{ $templateId ? 'fa-edit' : 'fa-plus-circle' }}"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Super Admin Console</span>
                        <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">WhatsApp Settings</span>
                    </div>
                    <h1 class="text-4xl font-[950] text-[#1a1235] uppercase tracking-tighter leading-none">
                        @if($templateId) Edit <span class="text-emerald-600">Meta Template</span> @else New <span class="text-emerald-600">Meta Template</span> @endif
                    </h1>
                </div>
            </div>
            <a href="{{ route('admin.whatsapp-templates.index') }}" wire:navigate class="px-8 py-5 bg-gray-50 text-gray-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-[#1a1235] hover:text-white transition-all shadow-sm border border-gray-100 leading-none flex items-center gap-3 group/btn">
                <i class="fas fa-arrow-left group-hover/btn:-translate-x-1 transition-transform"></i> Back to Templates
            </a>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        {{-- LEFT COLUMN: Form Inputs --}}
        <div class="lg:col-span-8 space-y-8">
            {{-- Template Specs Card --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10 space-y-8">
                <div class="flex items-center gap-4 pb-6 border-b border-gray-50">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg shadow-inner"><i class="fas fa-info-circle"></i></div>
                    <h3 class="text-[11px] font-black text-[#1a1235] uppercase tracking-[0.2em]">Basic Specifications</h3>
                </div>

                <div class="space-y-4">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Template Name (Must match Meta Name exactly)</label>
                    <input type="text" wire:model="name" placeholder="e.g., konfirmasi_pendaftaran_v1" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-base font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all placeholder:text-gray-300">
                    @error('name') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="space-y-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Category</label>
                        <select wire:model.live="category" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all">
                            <option value="transactional">Transactional</option>
                            <option value="auto_checkin">Auto Checkin</option>
                            <option value="event_invoice">Event Invoice</option>
                            <option value="reminder">Reminder</option>
                            <option value="certificate">Certificate</option>
                            <option value="event_feedback">Feedback</option>
                            <option value="broadcast">Global Broadcast</option>
                        </select>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Meta Category</label>
                        <select wire:model.live="meta_category" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all">
                            <option value="UTILITY">Utility</option>
                            <option value="MARKETING">Marketing</option>
                            <option value="AUTHENTICATION">Authentication</option>
                        </select>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Language Code</label>
                        <input type="text" wire:model="language_code" placeholder="e.g., id" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-sm font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all">
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Status</label>
                        <div class="flex p-1.5 bg-gray-50 rounded-2xl border border-gray-100">
                            <button type="button" wire:click="$set('is_active', true)" class="flex-1 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $is_active ? 'bg-emerald-600 text-white shadow-lg' : 'text-gray-400 hover:text-gray-600' }}">Active</button>
                            <button type="button" wire:click="$set('is_active', false)" class="flex-1 py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ !$is_active ? 'bg-slate-700 text-white shadow-lg' : 'text-gray-400 hover:text-gray-600' }}">Inactive</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Parameters & Component Configuration --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10 space-y-8">
                <div class="flex items-center gap-4 pb-6 border-b border-gray-50">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg shadow-inner"><i class="fas fa-sliders-h"></i></div>
                    <h3 class="text-[11px] font-black text-[#1a1235] uppercase tracking-[0.2em]">Message Structure & Variables</h3>
                </div>

                {{-- Header Config --}}
                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100/50 space-y-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-[11px] font-black text-[#1a1235] uppercase tracking-[0.2em] ml-1">Header Media (Optional)</label>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block ml-1 mt-0.5">Specify if you will include a media file in the header</span>
                            </div>
                            {{-- Loading Indicator for Header Type Change --}}
                            <div wire:loading wire:target="header_component_type" class="text-emerald-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 animate-pulse">
                                <i class="fas fa-spinner fa-spin"></i> Processing...
                            </div>
                        </div>
                        <select wire:model.live="header_component_type" class="block w-full px-8 py-5 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-[#1a1235] focus:outline-none focus:ring-4 focus:ring-emerald-50 focus:border-emerald-400 transition-all shadow-sm">
                            <option value="none">None</option>
                            <option value="image">Image (JPEG/PNG)</option>
                            <option value="video">Video (MP4)</option>
                            <option value="document">Document (PDF)</option>
                        </select>
                    </div>

                    @if(in_array($header_component_type, ['image', 'video']))
                        <div wire:key="media-upload-wrapper" class="space-y-4 pt-4 border-t border-slate-100 animate-fade-in">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Upload Media File (Max 10MB)</label>
                            
                            {{-- Existing File Preview --}}
                            @if($existing_header_url && !$header_file)
                                <div class="p-4 bg-white border border-slate-200 rounded-2xl flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        @if($header_component_type === 'image')
                                            <img src="{{ $existing_header_url }}" class="w-12 h-12 object-cover rounded-lg border border-slate-100">
                                        @else
                                            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center"><i class="fas fa-video text-slate-400"></i></div>
                                        @endif
                                        <div class="truncate leading-tight">
                                            <div class="text-xs font-bold text-[#1a1235]">Current Media File</div>
                                            <a href="{{ $existing_header_url }}" target="_blank" class="text-[9px] text-emerald-600 font-bold hover:underline truncate block">View File</a>
                                        </div>
                                    </div>
                                    <span class="text-[8px] bg-slate-100 text-slate-500 font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Active</span>
                                </div>
                            @endif

                            {{-- File Input --}}
                            <div class="relative group cursor-pointer border-2 border-dashed border-slate-200 hover:border-emerald-300 rounded-2xl p-6 text-center transition-all bg-white">
                                <input type="file" wire:model="header_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="space-y-2">
                                    <i class="fas fa-cloud-upload-alt text-slate-400 group-hover:text-emerald-500 text-2xl transition-all"></i>
                                    <div class="text-[11px] font-bold text-slate-500">
                                        @if($header_file)
                                            <span class="text-emerald-600">{{ $header_file->getClientOriginalName() }}</span>
                                        @else
                                            <span>Drag & drop or <span class="text-emerald-600 underline">browse</span> to upload</span>
                                        @endif
                                    </div>
                                    <div class="text-[9px] font-medium text-slate-400">Supports JPG, PNG, MP4 up to 10MB</div>
                                </div>
                            </div>
                            
                            {{-- Live Upload Progress --}}
                            <div wire:loading wire:target="header_file" class="text-emerald-600 text-[10px] font-bold uppercase tracking-wider block ml-1">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Uploading file to server...
                            </div>
                            @error('header_file') <p class="text-red-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                {{-- Body Preview --}}
                <div class="space-y-4">
                    <label class="block text-[11px] font-black text-[#1a1235] uppercase tracking-[0.2em] ml-1">Message Body Text (Matches Meta template text exactly)</label>
                    <textarea wire:model.live="body_preview" rows="4" placeholder="e.g., Selamat Datang {{1}}! Kehadiran Anda di {{2}} berhasil dicatat pada pukul {{3}}." class="block w-full px-8 py-6 bg-gray-50 border-transparent rounded-2xl text-xs leading-relaxed focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all"></textarea>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block ml-1">Use {{1}}, {{2}} to mark where the dynamic parameters go.</span>
                </div>

                {{-- Body Config --}}
                <div class="space-y-4">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Map Body Variables (Matches order of {{1}}, {{2}}... - Comma Separated)</label>
                    <textarea wire:model.live="body_params_text" rows="2" placeholder="e.g., name, event_name, ticket_code" class="block w-full px-8 py-6 bg-gray-50 border-transparent rounded-2xl text-xs font-mono leading-relaxed focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all"></textarea>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider block ml-1">Available parameters: name, event_name, ticket_code, event_instruction, date, time, location, total_bayar, payment_link, ticket_url</span>
                </div>

                {{-- Footer Config --}}
                <div class="space-y-4 pt-6 border-t border-slate-50">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Footer Text (Optional)</label>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block ml-1 -mt-2">Add a short line of text to the bottom of your message template (Max 60 characters)</span>
                    <input type="text" wire:model.live="footer_text" maxlength="60" placeholder="e.g., Sent via registrasi.events" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-xs font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-emerald-50 focus:border-emerald-400 transition-all placeholder:text-gray-300">
                </div>

                {{-- Button Config --}}
                <div class="space-y-6 pt-6 border-t border-slate-50">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Interactive Buttons (Optional)</label>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="addQuickReplyButton" class="px-4 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all flex items-center gap-1.5 shadow-sm leading-none">
                                <i class="fas fa-reply"></i> + Quick Reply
                            </button>
                            <button type="button" wire:click="addCtaButton" class="px-4 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl font-black text-[9px] uppercase tracking-widest transition-all flex items-center gap-1.5 shadow-sm leading-none">
                                <i class="fas fa-link"></i> + CTA Website
                            </button>
                        </div>
                    </div>

                    @foreach($buttons as $index => $btn)
                        <div wire:key="btn-config-{{ $index }}" class="p-5 bg-slate-50 border border-slate-100 rounded-2xl space-y-4 animate-fade-in">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-white text-slate-500 font-bold text-xs flex items-center justify-center border border-slate-150 shadow-sm">{{ $index + 1 }}</span>
                                    <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest">
                                        {{ $btn['type'] === 'quick_reply' ? '💬 Quick Reply Button' : '🔗 Website Link (CTA)' }}
                                    </span>
                                </div>
                                <button type="button" wire:click="removeButton({{ $index }})" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <i class="fas fa-times text-[10px]"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Button Text (Shown in chat)</label>
                                    <input type="text" wire:model.live="buttons.{{ $index }}.text" placeholder="e.g., Saya Hadir / Buka Website" class="block w-full px-5 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-[#1a1235]">
                                </div>

                                @if($btn['type'] === 'url')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="space-y-2">
                                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">URL Type</label>
                                            <select wire:model.live="buttons.{{ $index }}.url_type" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-[#1a1235]">
                                                <option value="static">Static (Fixed Link)</option>
                                                <option value="dynamic">Dynamic (Variable {{1}})</option>
                                                <option value="whatsapp_me">WhatsApp Me (Tautan WA)</option>
                                            </select>
                                        </div>

                                        @if(($btn['url_type'] ?? 'static') === 'dynamic')
                                            <div class="space-y-2">
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Parameter Map</label>
                                                <select wire:model.live="buttons.{{ $index }}.value" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-[#1a1235]">
                                                    <option value="ticket_url">ticket_url (Tiket Link)</option>
                                                    <option value="payment_link">payment_link (Invoice Link)</option>
                                                </select>
                                            </div>
                                        @elseif(($btn['url_type'] ?? 'static') === 'whatsapp_me')
                                            <div class="space-y-2 font-sans">
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">WhatsApp Phone Number</label>
                                                <input type="text" wire:model.live="buttons.{{ $index }}.static_url" placeholder="e.g., 6282120664105" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-[#1a1235]">
                                            </div>
                                        @else
                                            <div class="space-y-2 font-sans">
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Static Web Link (URL)</label>
                                                <input type="text" wire:model.live="buttons.{{ $index }}.static_url" placeholder="e.g., https://3flo.id" class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs font-bold text-[#1a1235]">
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="w-full py-6 bg-emerald-600 text-white rounded-[2rem] font-black text-sm uppercase tracking-[0.3em] shadow-xl hover:bg-emerald-700 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-4 group">
                <i class="fas fa-rocket group-hover:-translate-y-1 group-hover:translate-x-1 transition-transform"></i>
                Save Template
            </button>
        </div>

        {{-- RIGHT COLUMN: Realtime Preview & Editor Help --}}
        <div class="lg:col-span-4 space-y-8 sticky top-8">
            {{-- Preview Card --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-10 py-8 bg-emerald-50/20 border-b border-gray-50 flex items-center gap-4">
                    <i class="fab fa-whatsapp text-emerald-600 text-xl"></i>
                    <h3 class="text-[11px] font-black text-emerald-800 uppercase tracking-[0.2em]">Live UI Preview</h3>
                </div>
                <div class="p-6 bg-[#efeae2]" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-repeat: repeat; min-h-[350px] flex items-center justify-center">
                    <div class="w-full bg-white rounded-2xl p-4 text-[12px] shadow-md max-w-[90%] relative text-[#111b21] after:content-[''] after:absolute after:top-4 after:left-[-8px] after:w-0 after:h-0 after:border-8 after:border-transparent after:border-r-white">
                        {{-- Header Preview --}}
                        @if($header_component_type !== 'none')
                            @if($header_component_type === 'document')
                                <div class="mb-3 p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-3 text-[10px] text-slate-500 font-bold">
                                    <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                    <div class="leading-tight">
                                        <div class="text-[#1a1235] text-[11px]">E-Ticket_Receipt.pdf</div>
                                        <div class="text-[9px] font-medium text-slate-400 mt-0.5">PDF Document</div>
                                    </div>
                                </div>
                            @elseif($header_component_type === 'image')
                                @if($header_file)
                                    <img src="{{ $header_file->temporaryUrl() }}" class="w-full h-32 object-contain bg-transparent mb-3">
                                @elseif($existing_header_url)
                                    <img src="{{ $existing_header_url }}" class="w-full h-32 object-contain bg-transparent mb-3">
                                @else
                                    <div class="mb-3 p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-3 text-[10px] text-slate-500 font-bold">
                                        <i class="fas fa-image text-teal-500 text-2xl"></i>
                                        <div class="leading-tight">
                                            <div class="text-[#1a1235] text-[11px]">Static_Image.jpg</div>
                                            <div class="text-[9px] font-medium text-slate-400 mt-0.5">No image uploaded yet</div>
                                        </div>
                                    </div>
                                @endif
                            @elseif($header_component_type === 'video')
                                @if($header_file)
                                    <video src="{{ $header_file->temporaryUrl() }}" controls class="w-full h-32 object-contain bg-transparent mb-3"></video>
                                @elseif($existing_header_url)
                                    <video src="{{ $existing_header_url }}" controls class="w-full h-32 object-contain bg-transparent mb-3"></video>
                                @else
                                    <div class="mb-3 p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-3 text-[10px] text-slate-500 font-bold">
                                        <i class="fas fa-video text-indigo-500 text-2xl"></i>
                                        <div class="leading-tight">
                                            <div class="text-[#1a1235] text-[11px]">Static_Video.mp4</div>
                                            <div class="text-[9px] font-medium text-slate-400 mt-0.5">No video uploaded yet</div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif

                        {{-- Body Preview --}}
                        <div class="space-y-4">
                            <p class="whitespace-pre-line leading-relaxed font-sans">
                                @php
                                    $mockPool = [
                                        'name' => 'Budi Santoso',
                                        'event_name' => 'Seminar Teknologi Nasional 2026',
                                        'ticket_code' => 'REG-12345',
                                        'event_instruction' => 'Silakan datang ke registrasi meja 3',
                                        'date' => '17 Juli 2026',
                                        'time' => '14:30',
                                        'location' => 'Auditorium Utama Lantai 3',
                                        'total_bayar' => 'Rp 150.000',
                                        'payment_link' => 'invoice-uuid-example',
                                        'ticket_url' => 'ticket-uuid-example'
                                    ];

                                    // Parse parameter keys
                                    $paramsList = array_map('trim', explode(',', $body_params_text));
                                    $replacements = [];
                                    foreach ($paramsList as $idx => $key) {
                                        $placeholder = '{{' . ($idx + 1) . '}}';
                                        $replacements[$placeholder] = $mockPool[$key] ?? $placeholder;
                                    }

                                    $previewText = e($body_preview);
                                    $previewText = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $previewText);
                                    $previewText = preg_replace('/\_(.*?)\_/', '<em>$1</em>', $previewText);
                                    
                                    $previewText = strtr($previewText, $replacements);
                                    echo $previewText ?: 'Ketik body preview di sisi kiri untuk melihat di sini...';
                                @endphp
                            </p>
                            
                            {{-- Footer Preview --}}
                            @if(!empty($footer_text))
                                <div class="text-[10px] text-gray-400 mt-2 font-sans border-t border-slate-100 pt-1.5 leading-relaxed">
                                    {{ $footer_text }}
                                </div>
                            @endif

                            <span class="text-[9px] text-gray-400 float-right mt-1 font-medium">14:30</span>
                        </div>

                        {{-- Buttons Preview --}}
                        @if(!empty($buttons))
                            <div class="mt-4 pt-2 border-t border-slate-50 space-y-1">
                                @foreach($buttons as $btn)
                                    <div class="bg-white hover:bg-slate-50 text-[#00a884] py-3 rounded-2xl text-center text-[12px] font-bold shadow-sm border border-slate-100/60 cursor-pointer flex items-center justify-center gap-2">
                                        @if($btn['type'] === 'url')
                                            <i class="fas fa-external-link-alt text-[10px] text-[#00a884]/60"></i>
                                        @else
                                            <i class="fas fa-reply text-[10px] text-[#00a884]/60"></i>
                                        @endif
                                        <span>{{ !empty($btn['text']) ? $btn['text'] : 'Button Label' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tip Card --}}
            <div class="bg-[#1a1235] rounded-[2.5rem] p-10 text-white relative overflow-hidden">
                <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl"></div>
                <div class="relative z-10 space-y-6">
                    <h4 class="text-[11px] font-black uppercase tracking-[0.3em] text-white/50">Developer Guidelines</h4>
                    <div class="space-y-4 text-xs leading-relaxed text-white/70">
                        <p>1. **Nama Template**: Gunakan huruf kecil, angka, dan underscore saja. Pastikan nama template yang didaftarkan sama persis dengan yang disetujui di Dashboard Meta Developer console (contoh: `konfirmasi_pendaftaran_v1`).</p>
                        <p>2. **Bahasa**: Secara default menggunakan `id` untuk bahasa Indonesia.</p>
                        <p>3. **Kategori**: Tentukan kategori pemicu dengan tepat agar template ini dapat dipilih oleh Organizer untuk aksi yang sesuai di event mereka.</p>
                        <p>4. **Aturan Letak Variabel (Meta)**: Variabel parameter (seperti `{{4}}`) **TIDAK boleh diletakkan di akhir baris/paragraf** tanpa ditutup kalimat/kata berisi kata-kata. Pastikan ada kata penutup setelah variabel pada baris/paragraf yang sama (contoh: `📍 Lokasi acara di {{4}}. Sampai jumpa di lokasi!`).</p>
                        <p>5. **UTILITY vs MARKETING**: Kategori **UTILITY** melarang keras penggunaan kalimat kasual/sapaan ramah, tanda seru (!), emoji, dan footer nama brand. Jika Anda menggunakan unsur-unsur ramah/desain tersebut, pastikan memilih **Meta Category: MARKETING** agar tidak ditolak otomatis oleh Meta (*Incorrect Category*).</p>
                        <p>6. **Contoh Tombol Dinamis**: Pada tombol berjenis URL Dinamis (`Lihat E-Tiket`), parameter contoh (`example`) yang dikirim ke Meta akan diisi otomatis sebagai string parameter sederhana saja (seperti `sample-uuid`), bukan URL lengkap, demi mematuhi validasi Meta.</p>
                        <p>7. **Tautan WhatsApp Me**: Tautan langsung ke WhatsApp (`wa.me`) dilarang oleh Meta di dalam tombol. Sistem ini secara otomatis memotongnya dengan membuat tautan proxy redirect aman (`registrasi.events/chat-wa/...`) saat diajukan ke Meta.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

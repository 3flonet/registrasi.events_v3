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

<div class="max-w-none mx-auto pb-12 font-sans" x-data="{ 
    content: @entangle('content'), 
    subject: @entangle('subject'), 
    mode: 'visual',
    lastFocused: 'visual',
    tags: {
        'Event Basics': [
            { code: '{event_name}', label: 'Event Title' },
            { code: '{organizer}', label: 'Organizer Name' },
            { code: '{date}', label: 'Event Date' }
        ],
        'Smart Placeholders': [
            { code: '{name}', label: 'Participant Name' },
            { code: '{jabatan}', label: 'Position / Job Title' },
            { code: '{company}', label: 'Organization / Company' }
        ],
        'Ticketing & Links': [
            { code: '{ticket_code}', label: 'Unique Ticket ID' },
            { code: '{link_ticket}', label: 'Ticket Link URL' },
            { code: '{app_name}', label: 'Application Name' }
        ]
    },
    init() {
        this.$watch('mode', value => {
            if (value === 'preview') {
                const frame = this.$refs.previewFrame;
                const doc = frame.contentDocument || frame.contentWindow.document;
                doc.open();
                doc.write('<!DOCTYPE html><html><head><style>body { margin: 0; padding: 0; }</style></head><body>' + this.content + '</body></html>');
                doc.close();
            }
        });
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
        } else if (this.lastFocused === 'subject') {
            const el = this.$refs.subjectField;
            if (el) {
                const start = el.selectionStart;
                const end = el.selectionEnd;
                this.subject = this.subject.substring(0, start) + tag + this.subject.substring(end);
                el.focus();
                setTimeout(() => { el.selectionStart = el.selectionEnd = start + tag.length; }, 10);
            }
        }
    }
}">
    {{-- Header Section --}}
    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 mb-10 overflow-hidden relative group animate-fade-in">
        <div class="absolute top-0 right-0 p-10 opacity-[0.03] -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-1000">
            <i class="fas fa-magic text-[200px] rotate-12 text-indigo-900"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">
            <div class="flex items-center gap-8">
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-50 to-purple-50 text-indigo-600 rounded-3xl flex items-center justify-center text-3xl shadow-inner group-hover:rotate-6 transition-transform">
                    <i class="fas {{ $templateId ? 'fa-pen-nib' : 'fa-plus-circle' }}"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Broadcast Hub</span>
                        <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Broadcast Templates</span>
                    </div>
                    <h1 class="text-4xl font-[950] text-[#1a1235] uppercase tracking-tighter leading-none">
                        @if($templateId) Edit <span class="text-indigo-600">Template</span> @else New <span class="text-indigo-600">Template</span> @endif
                    </h1>
                </div>
            </div>
            <a href="{{ route('admin.global-broadcast') }}" wire:navigate class="px-8 py-5 bg-gray-50 text-gray-500 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-[#1a1235] hover:text-white transition-all shadow-sm border border-gray-100 leading-none flex items-center gap-3 group/btn">
                <i class="fas fa-arrow-left group-hover/btn:-translate-x-1 transition-transform"></i> Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <form wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start animate-fade-in">
        
        {{-- LEFT COLUMN: Inputs --}}
        <div class="lg:col-span-8 space-y-8">
            
            {{-- Template Identity Card --}}
            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md animate-fade-in">
                <div class="space-y-10">
                    {{-- Subject --}}
                    <div class="space-y-4">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Template Subject / Internal Name</label>
                        <input type="text" x-model="subject" x-ref="subjectField" @focus="lastFocused = 'subject'" class="block w-full px-8 py-5 bg-gray-50 border-transparent rounded-2xl text-base font-bold text-[#1a1235] focus:bg-white focus:ring-8 focus:ring-indigo-100 transition-all placeholder:text-gray-300 shadow-inner" placeholder="Enter transmission subject...">
                        @error('subject') <span class="text-red-500 text-[9px] font-black mt-2 block uppercase tracking-widest animate-pulse">{{ $message }}</span>@enderror
                    </div>

                    {{-- Template Type Selector --}}
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Template Type</label>
                        <div class="flex p-1.5 bg-gray-50 rounded-2xl border border-gray-100 w-fit">
                            <button type="button" wire:click="$set('type', 'email')" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $type === 'email' ? 'bg-[#1a1235] text-white shadow-lg' : 'text-gray-400 hover:text-gray-600' }}">Email Only</button>
                            <button type="button" wire:click="$set('type', 'whatsapp')" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $type === 'whatsapp' ? 'bg-emerald-500 text-white shadow-lg' : 'text-gray-400 hover:text-gray-600' }}">WhatsApp Only</button>
                            <button type="button" wire:click="$set('type', 'both')" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $type === 'both' ? 'bg-indigo-500 text-white shadow-lg' : 'text-gray-400 hover:text-gray-600' }}">Both (Email & WA)</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WhatsApp Template Selection & Preview --}}
            @if($type === 'whatsapp' || $type === 'both')
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md animate-fade-in">
                    <div class="px-10 py-8 border-b border-gray-50 bg-emerald-50/20 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 shadow-sm"><i class="fab fa-whatsapp text-xl"></i></div>
                            <h3 class="text-[11px] font-black text-emerald-800 uppercase tracking-[0.2em]">Meta WhatsApp Template</h3>
                        </div>
                    </div>
                    <div class="p-10 space-y-6">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-emerald-800 uppercase tracking-[0.2em] ml-1">Select Meta Template</label>
                            <select wire:model.live="whatsapp_template_id" class="block w-full px-5 py-4 bg-white border border-emerald-100 rounded-2xl text-sm font-semibold text-[#1a1235] focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 transition-all">
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
                                <div class="p-6 bg-white border border-emerald-500/5 rounded-3xl space-y-4 animate-fade-in shadow-sm">
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
                                            <h4 class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest">{{ strtoupper($headerType) }} Header File</h4>
                                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">This template requires a dynamic header file</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <input type="file" wire:model="whatsapp_media_file" class="block w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-teal-50 file:text-teal-600 hover:file:bg-teal-100 cursor-pointer">
                                        </div>
                                        @if($existingWhatsappMediaUrl)
                                            <div class="text-right">
                                                <a href="{{ Storage::url($existingWhatsappMediaUrl) }}" target="_blank" class="inline-flex items-center gap-2 text-[9px] font-black text-teal-600 uppercase tracking-widest hover:underline">
                                                    <i class="fas fa-external-link-alt"></i> View Current File
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    @error('whatsapp_media_file') <p class="text-red-500 text-[9px] font-bold mt-1 uppercase tracking-widest">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            @if($whatsappParameters && count($whatsappParameters) > 0)
                                <div class="p-6 bg-white border border-emerald-500/5 rounded-3xl space-y-6 animate-fade-in shadow-sm">
                                    <h4 class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest border-b pb-2">Map Template Variables</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($whatsappParameters as $paramName => $paramValue)
                                            @if($paramName !== 'header')
                                                <div class="space-y-2">
                                                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest">Variable: {{ $paramValue['key'] }}</label>
                                                    <select wire:model="whatsappParameters.{{ $paramName }}.value" class="block w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-xs font-semibold text-[#1a1235] focus:bg-white focus:ring-4 focus:ring-emerald-100 transition-all">
                                                        <option value="">-- Choose Field Mapping --</option>
                                                        <option value="event_name">Event Name</option>
                                                        <option value="ticket_url">Ticket URL Suffix (UUID)</option>
                                                        <option value="payment_link">Payment/Invoice Link Suffix (UUID)</option>
                                                        <option value="name">Participant Name</option>
                                                        <option value="ticket_code">Ticket Code</option>
                                                    </select>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            {{-- Email Editor Card --}}
            @if($type === 'email' || $type === 'both')
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md animate-fade-in">
                    <div class="px-10 py-8 border-b border-gray-50 bg-indigo-50/20 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm"><i class="fas fa-envelope-open-text"></i></div>
                            <h3 class="text-[11px] font-black text-indigo-800 uppercase tracking-[0.2em]">HTML Email Template</h3>
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
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Email Content (HTML)</label>
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
            @endif

            {{-- Save / Commit synthesis button at the bottom of Left Column --}}
            <button type="submit" class="w-full py-6 bg-[#1a1235] text-white rounded-[2rem] font-black text-sm uppercase tracking-[0.3em] shadow-xl hover:bg-indigo-600 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-4 group relative z-10">
                <i class="fas fa-save group-hover:scale-110 transition-transform"></i>
                Save Template
            </button>
        </div>

        {{-- RIGHT COLUMN: Magic Library & Actions --}}
        <div class="lg:col-span-4 space-y-8">
            
            {{-- Operational Integrity Info --}}
            <div class="bg-[#1a1235] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl"></div>
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-300">Broadcast Info</span>
                    </div>
                    <div>
                        <p class="text-[9px] text-white/50 font-medium uppercase tracking-[0.1em] mb-1">Target Audience</p>
                        <h5 class="text-2xl font-black tracking-tighter uppercase">Global Broadcast</h5>
                    </div>
                </div>
            </div>

            {{-- Dynamic Tags Card (Interactive Click-to-Insert) --}}
            <div class="bg-[#1a1235] rounded-[2.5rem] shadow-2xl p-10 overflow-hidden relative group">
                <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-indigo-600/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <h4 class="text-[11px] font-black uppercase tracking-[0.3em] text-white/50">Dynamic Tags</h4>
                        <span class="px-2 py-1 bg-white/5 rounded text-[8px] font-bold text-white/30 uppercase">Click to Insert</span>
                    </div>
                    <div class="space-y-6 max-h-[500px] overflow-y-auto no-scrollbar pr-2">
                        <template x-for="(group, name) in tags" :key="name">
                            <div class="space-y-3">
                                <p class="text-[8px] font-black uppercase tracking-[0.2em] text-indigo-400/60 ml-1" x-text="name"></p>
                                <div class="space-y-2">
                                    <template x-for="tag in group" :key="tag.code">
                                        <button type="button" @click="insertTag(tag.code)" onmousedown="event.preventDefault()" class="w-full flex items-center justify-between p-3.5 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/10 hover:border-indigo-400 transition-all group/tag text-left">
                                            <code class="text-indigo-300 text-[10px] font-mono font-black italic group-hover/tag:scale-105 transition-transform" x-text="tag.code"></code>
                                            <span class="text-[7px] text-white/40 font-bold uppercase tracking-widest" x-text="tag.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="max-w-none mx-auto pb-12">
    {{-- MODAL VIEW SUBMISSION DATA --}}
    <div x-data="{ 
                show: false,
                guestName: '',
                submissionData: {},
                mediaFiles: []
            }"
        x-on:open-submission-modal.window="show = true; submissionData = $event.detail.submission; guestName = $event.detail.name; mediaFiles = $event.detail.media"
        x-show="show"
        class="fixed inset-0 z-[120] overflow-y-auto"
        style="display: none;">
        <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity" @click="show = false"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-2xl rounded-[2rem] shadow-2xl overflow-hidden animate-bounce-in flex flex-col max-h-[90vh]">
                <div class="p-8 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center text-xl shadow-lg shadow-indigo-100">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-[#1a1235] uppercase tracking-widest">Guest Response Details</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1" x-text="guestName"></p>
                        </div>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-red-500 transition-colors">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </button>
                </div>

                <div class="p-10 overflow-y-auto custom-scrollbar flex-1">
                    <div class="grid grid-cols-1 gap-6">
                        <template x-for="(value, key) in submissionData" :key="key">
                            <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                                <label class="block text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2" x-text="key.replace(/_/g, ' ')"></label>
                                
                                <template x-if="value === '[Digital Signature Attached]'">
                                    <div class="mt-2 bg-white p-4 rounded-xl border border-gray-100 flex items-center justify-center min-h-[100px]">
                                        <template x-for="file in mediaFiles" :key="file.url">
                                            <template x-if="(file.name && file.name.toLowerCase().includes('signature_' + key.toLowerCase())) || (file.file_name && file.file_name.toLowerCase().includes('signature_' + key.toLowerCase()))">
                                                <img :src="file.url" class="max-h-32 w-auto object-contain" alt="Signature">
                                            </template>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="value !== '[Digital Signature Attached]'">
                                    <p class="text-sm font-bold text-[#1a1235]" x-text="Array.isArray(value) ? value.join(', ') : (value || '-')"></p>
                                </template>
                            </div>
                        </template>

                        <template x-if="mediaFiles.length > 0">
                            <div class="mt-4">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Attached Files / Documents</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="file in mediaFiles" :key="file.url">
                                        <template x-if="!file.name.includes('signature_')">
                                            <a :href="file.url" target="_blank" class="flex items-center gap-4 p-4 bg-white border border-gray-100 rounded-2xl hover:border-indigo-600 transition-all group shadow-sm">
                                                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                                    <i class="fas fa-file-download"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-black text-[#1a1235] uppercase tracking-tight truncate" x-text="file.name"></p>
                                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Click to view/download</p>
                                                </div>
                                            </a>
                                        </template>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-if="Object.keys(submissionData).length === 0 && mediaFiles.length === 0">
                            <div class="py-12 text-center">
                                <i class="fas fa-ghost text-4xl text-gray-100 mb-4 block"></i>
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No additional data submitted.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button @click="show = false" class="px-8 py-4 bg-[#1a1235] text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-100">Close Details</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Header Section --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">Invitation Manager</h1>
                <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage guest invitations and RSVPs for: <span class="text-indigo-600 font-black">{{ $event->name }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.events.index') }}" wire:navigate class="px-6 py-4 bg-gray-50 text-gray-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all leading-none">
                    <i class="fas fa-arrow-left mr-2 font-black"></i> Return to Events
                </a>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 15000)" class="bg-emerald-600 text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center justify-between animate-bounce-in border border-emerald-400 relative overflow-hidden">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <span class="font-bold uppercase tracking-widest text-[10px]">{{ session('message') }}</span>
        </div>
        <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
        <div class="absolute bottom-0 left-0 h-1 bg-white/20 transition-all duration-[15000ms] ease-linear" :style="show ? 'width: 100%' : 'width: 0%'" x-init="setTimeout(() => $el.style.width = '0%', 100)"></div>
    </div>
    @endif

    @if (session()->has('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 60000)" class="bg-rose-600 text-white px-8 py-4 rounded-2xl shadow-lg mb-8 flex items-center justify-between animate-bounce-in border border-rose-400 relative overflow-hidden">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
            <span class="font-bold uppercase tracking-widest text-[10px]">{{ session('error') }}</span>
        </div>
        <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
        <div class="absolute bottom-0 left-0 h-1 bg-white/20 transition-all duration-[60000ms] ease-linear" :style="show ? 'width: 100%' : 'width: 0%'" x-init="setTimeout(() => $el.style.width = '0%', 100)"></div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- KOLOM KIRI: Configuration Hub --}}
        <div class="lg:col-span-4 space-y-8">

            {{-- Data Ingestion Module --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-100 flex items-center justify-between">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">01. Import Guests</h3>
                    <button wire:click="downloadTemplate" class="text-[8px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest flex items-center gap-1">
                        <i class="fas fa-download"></i> Excel Template
                    </button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="p-6 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 text-center">
                        <label for="excel_file" class="cursor-pointer block">
                            <i class="fas fa-file-excel text-3xl text-gray-200 mb-4 block"></i>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Select Excel File (.xlsx)</span>
                            <input type="file" id="excel_file" wire:model="file" class="hidden">
                            @if($file)
                            <div class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase tracking-widest truncate max-w-full inline-block">{{ $file->getClientOriginalName() }}</div>
                            @endif
                        </label>
                    </div>
                    @error('file') <span class="text-red-500 text-[9px] font-bold block text-center">{{ $message }}</span> @enderror

                    <button wire:click="import" wire:loading.attr="disabled" class="w-full py-4 bg-[#1a1235] text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100 leading-none">
                        <span wire:loading.remove wire:target="import, file">Import Now</span>
                        <span wire:loading wire:target="import, file italic text-[8px]">Processing Import...</span>
                    </button>
                </div>
            </div>

            {{-- Messaging & Asset Studio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                x-data="{ 
                    tab: 'wa',
                    quill: null,
                    emailQuill: null,
                    initQuill() {
                        if (this.quill) return;
                        
                        // Force Quill to use Inline Styles instead of Classes for alignment
                        const AlignStyle = Quill.import('attributors/style/align');
                        Quill.register(AlignStyle, true);

                        this.quill = new Quill($refs.quillEditor, {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'color': [] }, { 'background': [] }],
                                    [{ 'align': [] }],
                                    ['link', 'blockquote', 'code-block'],
                                    ['clean']
                                ]
                            }
                        });
                        this.quill.root.innerHTML = $wire.get('letterBody') || '';
                        this.quill.on('text-change', () => {
                            $wire.set('letterBody', this.quill.root.innerHTML);
                        });
                    },
                    initEmailQuill() {
                        if (this.emailQuill) return;

                        // Force Quill to use Inline Styles instead of Classes for alignment
                        const AlignStyle = Quill.import('attributors/style/align');
                        Quill.register(AlignStyle, true);

                        this.emailQuill = new Quill($refs.emailEditor, {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'color': [] }, { 'background': [] }],
                                    [{ 'align': [] }],
                                    ['link', 'blockquote'],
                                    ['clean']
                                ]
                            }
                        });
                        this.emailQuill.root.innerHTML = $wire.get('emailBody') || '';
                        this.emailQuill.on('text-change', () => {
                            $wire.set('emailBody', this.emailQuill.root.innerHTML);
                        });
                    },
                    insertWaTag(tag) {
                        const textarea = $refs.waTextarea;
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        const text = textarea.value;
                        const newText = text.substring(0, start) + tag + text.substring(end);
                        $wire.set('waTemplate', newText);
                        setTimeout(() => {
                            textarea.focus();
                            textarea.setSelectionRange(start + tag.length, start + tag.length);
                        }, 0);
                    },
                    insertRsvpTag(tag) {
                        const textarea = $refs.rsvpTextarea;
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        const text = textarea.value;
                        const newText = text.substring(0, start) + tag + text.substring(end);
                        $wire.set('confirmGreeting', newText);
                        setTimeout(() => {
                            textarea.focus();
                            textarea.setSelectionRange(start + tag.length, start + tag.length);
                        }, 0);
                    },
                    applyWaStyle(symbol) {
                        const textarea = $refs.waTextarea;
                        const start = textarea.selectionStart;
                        const end = textarea.selectionEnd;
                        const text = textarea.value;
                        const selectedText = text.substring(start, end);
                        const newText = text.substring(0, start) + symbol + selectedText + symbol + text.substring(end);
                        $wire.set('waTemplate', newText);
                        setTimeout(() => {
                            textarea.focus();
                            const newPos = end + (symbol.length * 2);
                            textarea.setSelectionRange(newPos, newPos);
                        }, 0);
                    },
                    insertEmailTag(tag) {
                        if (this.emailQuill) {
                            const range = this.emailQuill.getSelection(true);
                            this.emailQuill.insertText(range.index, tag);
                        }
                    }
                  }"
                x-init="
                        $watch('tab', value => { 
                            if (value === 'letter') { setTimeout(() => initQuill(), 100); } 
                            if (value === 'email') { setTimeout(() => initEmailQuill(), 100); }
                        })">
                <div class="p-6 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">02. Message & Letters</h3>
                </div>

                {{-- Tab Switcher --}}
                <div class="flex p-2 bg-gray-50 gap-1">
                    <button @click="tab = 'wa'" :class="tab === 'wa' ? 'bg-[#1a1235] text-white shadow-md' : 'text-gray-400 hover:bg-gray-100'" class="flex-1 py-3 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">WhatsApp</button>
                    <button @click="tab = 'email'" :class="tab === 'email' ? 'bg-[#1a1235] text-white shadow-md' : 'text-gray-400 hover:bg-gray-100'" class="flex-1 py-3 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Email</button>
                    <button @click="tab = 'letter'" :class="tab === 'letter' ? 'bg-[#1a1235] text-white shadow-md' : 'text-gray-400 hover:bg-gray-100'" class="flex-1 py-3 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all text-nowrap">E-Letter</button>
                    <button @click="tab = 'rsvp'" :class="tab === 'rsvp' ? 'bg-[#1a1235] text-white shadow-md' : 'text-gray-400 hover:bg-gray-100'" class="flex-1 py-3 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all text-nowrap">RSVP Page</button>
                </div>

                <div class="p-8">
                    {{-- ... WHATSAPP & EMAIL CONTENT ... --}}
                    <div x-show="tab === 'wa'" class="space-y-6 animate-bounce-in">
                        {{-- WhatsApp Toolbar --}}
                        <div class="flex items-center gap-2 mb-3 bg-gray-50 p-2 rounded-xl border border-gray-100">
                            <button type="button" @click="applyWaStyle('*')" class="p-2 hover:bg-white hover:text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1">
                                <i class="fas fa-bold"></i> Bold
                            </button>
                            <button type="button" @click="applyWaStyle('_')" class="p-2 hover:bg-white hover:text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1">
                                <i class="fas fa-italic"></i> Italic
                            </button>
                            <button type="button" @click="applyWaStyle('~')" class="p-2 hover:bg-white hover:text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1">
                                <i class="fas fa-strikethrough"></i> Strike
                            </button>
                            <button type="button" @click="applyWaStyle('```')" class="p-2 hover:bg-white hover:text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1">
                                <i class="fas fa-code"></i> Mono
                            </button>
                        </div>

                        <textarea wire:model="waTemplate" x-ref="waTextarea" rows="8" class="w-full p-4 bg-gray-50 border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-green-500 transition-all custom-scrollbar resize-none"></textarea>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-4">
                            @foreach(['{name}', '{company}', '{jabatan}', '{event_name}', '{link_surat}', '{link_konfirmasi}', '{link_lampiran}'] as $var)
                            <button type="button" @click="insertWaTag('{{ $var }}')"
                                class="px-3 py-2 bg-gray-50 rounded-lg text-[9px] font-black text-gray-400 hover:bg-emerald-600 hover:text-white transition-all border border-gray-100 uppercase tracking-widest leading-none">
                                {{ $var }}
                            </button>
                            @endforeach
                        </div>

                        <button wire:click="saveMessageSettings" class="w-full py-4 bg-emerald-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-700 transition-all leading-none mt-6">Save Message Settings</button>
                    </div>

                    <div x-show="tab === 'email'" class="space-y-6 animate-bounce-in">
                        <div class="space-y-6">
                            {{-- Invitation Email Banner --}}
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Email Banner (Invitation Only) <span class="text-indigo-400 ml-1 text-[8px] font-bold italic">- Max 2MB (JPG/PNG)</span></label>
                                <div class="relative group">
                                    <input type="file" wire:model="invitationEmailBanner" class="absolute inset-0 w-full h-full opacity-0 z-50 cursor-pointer">
                                    <div class="relative w-full h-32 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-blue-400 group-hover:bg-blue-50/30">
                                        @if($invitationEmailBanner)
                                        <img src="{{ $invitationEmailBanner->temporaryUrl() }}" class="w-full h-full object-cover">
                                        @elseif($existingEmailBannerPath)
                                        <img src="{{ Storage::url($existingEmailBannerPath) }}" class="w-full h-full object-cover">
                                        @else
                                        <div class="flex flex-col items-center gap-1 text-gray-300">
                                            <i class="fas fa-image text-2xl"></i>
                                            <span class="text-[8px] font-black uppercase tracking-widest">Click to upload banner</span>
                                        </div>
                                        @endif
                                        <div wire:loading wire:target="invitationEmailBanner" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center">
                                            <svg class="animate-spin h-5 w-5 text-blue-600" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                @error('invitationEmailBanner') <p class="text-red-500 text-[8px] font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Subject</label>
                                <input type="text" wire:model="emailSubject" class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold text-[#1a1235] focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Body Content</label>
                            <div class="relative rounded-2xl border border-gray-100 overflow-hidden shadow-sm bg-white" wire:ignore x-init="setTimeout(() => initEmailQuill(), 100)">
                                <div x-ref="emailEditor" style="height: 300px;" class="text-sm"></div>
                            </div>
                        </div>

                        <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                                <i class="fas fa-magic"></i> Available Tags
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['{name}', '{company}', '{jabatan}', '{event_name}', '{link_surat}', '{link_konfirmasi}', '{link_lampiran}', '{btn_lampiran}', '{btn_surat}', '{btn_konfirmasi}', '{banner}'] as $var)
                                <button type="button" @click="insertEmailTag('{{ $var }}')" class="px-2 py-1 bg-white border border-blue-100 rounded text-[8px] font-black text-blue-600 hover:bg-blue-600 hover:text-white transition-all uppercase tracking-widest leading-none">{{ $var }}</button>
                                @endforeach
                            </div>
                        </div>
                        <button wire:click="saveMessageSettings" class="w-full py-4 bg-blue-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 leading-none mt-4">Save Email Configuration</button>
                    </div>

                    <div x-show="tab === 'rsvp'" class="space-y-6 animate-bounce-in">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">RSVP Page Greeting</label>
                                <textarea wire:model="confirmGreeting" x-ref="rsvpTextarea" rows="6" class="w-full p-4 bg-gray-50 border-none rounded-xl text-xs font-medium focus:ring-2 focus:ring-purple-500 transition-all resize-none"></textarea>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-4">
                                    @foreach(['{name}', '{company}', '{jabatan}', '{event_name}'] as $var)
                                    <button type="button" @click="insertRsvpTag('{{ $var }}')" class="px-3 py-2 bg-gray-50 rounded-lg text-[9px] font-black text-gray-400 hover:bg-purple-600 hover:text-white transition-all border border-gray-100 uppercase tracking-widest leading-none">
                                        {{ $var }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <button wire:click="saveMessageSettings" class="w-full py-4 bg-purple-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-purple-700 transition-all leading-none">Save RSVP Settings</button>
                    </div>

                    {{-- E-LETTER --}}
                    <div x-show="tab === 'letter'" class="space-y-6 animate-bounce-in">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Letterhead Image (Kop Surat) <span class="text-indigo-400 ml-1 text-[8px] font-bold italic">- Max 2MB (JPG/PNG)</span></label>
                            <div class="p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 text-center relative group">
                                @if($newLetterHeader)
                                <img src="{{ $newLetterHeader->temporaryUrl() }}" class="h-24 object-contain mx-auto rounded shadow-sm group-hover:opacity-50 transition-all">
                                @elseif($existingLetterHeader)
                                <img src="{{ asset('storage/' . $existingLetterHeader) }}" class="h-24 object-contain mx-auto rounded shadow-sm group-hover:opacity-50 transition-all">
                                @else
                                <div class="py-4">
                                    <i class="fas fa-scroll text-3xl text-gray-200 mb-2 block"></i>
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">No Header Asset</span>
                                </div>
                                @endif
                                <input type="file" id="letter_header" wire:model="newLetterHeader" class="hidden">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                    <label for="letter_header" class="px-4 py-2 bg-[#1a1235] text-white text-[9px] font-black uppercase tracking-widest rounded-lg cursor-pointer shadow-xl">Replace Image</label>
                                </div>
                            </div>
                            @if($existingLetterHeader)
                            <button type="button" wire:click="confirmDeleteHeader" class="text-[8px] font-black text-red-500 uppercase tracking-widest hover:underline block mx-auto mt-2">Delete Header Asset</button>
                            @endif

                            <div class="space-y-3 pt-6 border-t border-gray-50">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Letter Body Content</label>
                                    <span class="text-[8px] font-bold text-indigo-400 bg-indigo-50 px-2 py-0.5 rounded uppercase tracking-widest">Supports HTML & Variables</span>
                                </div>

                                <div class="relative rounded-2xl border border-gray-100 overflow-hidden shadow-sm bg-white" wire:ignore>
                                    <div x-ref="quillEditor" style="height: 350px;" class="text-sm"></div>
                                </div>

                                <div class="p-4 bg-indigo-50/50 rounded-xl border border-indigo-100/50">
                                    <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-[0.1em] mb-3 flex items-center gap-2">
                                        <i class="fas fa-magic"></i> Available Placeholders
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(['{name}', '{company}', '{jabatan}', '{event_name}', '{link_konfirmasi}'] as $v)
                                        <button type="button" @click="
                                                    const range = quill.getSelection(true);
                                                    quill.insertText(range.index, '{{ $v }}');
                                                " class="px-2 py-1 bg-white border border-indigo-100 rounded text-[8px] font-black text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all uppercase tracking-widest leading-none">{{ $v }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 pt-6 border-t border-gray-50">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Digital Attachments (Lampiran) <span class="text-indigo-400 ml-1 text-[8px] font-bold italic">- Max 10MB per file</span></label>
                                <input type="file" wire:model="newAttachments" multiple class="block w-full text-[10px] text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[9px] file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer">

                                @if(!empty($existingAttachments))
                                <div class="space-y-2 mt-4">
                                    @foreach($existingAttachments as $index => $path)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <span class="text-[9px] font-bold text-gray-500 truncate max-w-[200px]">{{ basename($path) }}</span>
                                        <button type="button" wire:click="removeAttachment({{ $index }})" class="text-red-400 hover:text-red-600">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 mt-8">
                            <button wire:click="saveLetterSettings" class="flex-1 py-4 bg-purple-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-purple-700 transition-all shadow-xl shadow-purple-100 leading-none">Save Global Configuration</button>
                            <button @click="$dispatch('open-preview')" type="button" class="px-6 py-4 bg-white border border-purple-200 text-purple-600 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-purple-50 transition-all leading-none">
                                <i class="fas fa-eye mr-2"></i> Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Guest Roster Canvas --}}
        <div class="lg:col-span-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex flex-col lg:flex-row items-center justify-between gap-6 bg-gray-50/30">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Invitation List</h3>
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                {{-- BROADCAST BUTTONS --}}
                <div class="flex items-center gap-1 bg-white p-1 rounded-xl shadow-sm border border-gray-100">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center gap-2 hover:bg-indigo-700 transition-all">
                            <i class="fas fa-bullhorn"></i> Broadcast
                            <i class="fas fa-chevron-down text-[7px]"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden animate-bounce-in">
                            <button wire:click="broadcastEmails(true)" @click="open = false" class="w-full text-left px-4 py-3 text-[9px] font-black text-gray-600 uppercase tracking-widest hover:bg-indigo-50 hover:text-indigo-600 transition-all border-b border-gray-50 flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                                    <i class="far fa-envelope text-sm"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span>Broadcast Email</span>
                                    <span class="text-[7px] text-gray-400 normal-case font-bold tracking-normal">Kirim ke tamu yang belum menerima email</span>
                                </div>
                            </button>
                            <button wire:click="broadcastWhatsApp(true)" @click="open = false" class="w-full text-left px-4 py-3 text-[9px] font-black text-gray-600 uppercase tracking-widest hover:bg-emerald-50 hover:text-emerald-600 transition-all border-b border-gray-50 flex items-center gap-3">
                                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                                    <i class="fab fa-whatsapp text-base"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span>Broadcast WhatsApp (Queue)</span>
                                    <span class="text-[7px] text-gray-400 normal-case font-bold tracking-normal">Kirim via antrean sistem (Otomatis)</span>
                                </div>
                            </button>
                            <button wire:click="broadcastWhatsAppWeb(true)" @click="open = false" class="w-full text-left px-4 py-3 text-[9px] font-black text-gray-600 uppercase tracking-widest hover:bg-indigo-50 hover:text-indigo-600 transition-all flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-globe text-sm"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span>Broadcast WhatsApp (Direct Web)</span>
                                    <span class="text-[7px] text-indigo-400 normal-case font-bold tracking-normal">Kirim langsung via Browser (Lebih Aman)</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <select wire:model.live="filterStatus" class="px-4 py-2 bg-white border-none rounded-xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer shadow-sm border border-gray-100">
                    <option value="all">All Guests</option>
                    <option value="pending">Waiting Response</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="represented">Represented</option>
                    <option value="declined">Declined</option>
                </select>
                <div class="relative flex-grow lg:w-48">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..." class="w-full pl-10 pr-4 py-2 bg-white border-none rounded-xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm border border-gray-100">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[10px]"></i>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto border-t border-gray-100">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Guest & Company</th>
                        <th class="px-8 py-5 text-left text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Contact Info</th>
                        <th class="px-8 py-5 text-center text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-right text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($invitations as $invite)
                    <tr class="group hover:bg-indigo-50/30 transition-all border-l-4 border-transparent hover:border-indigo-600">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-[#1a1235] uppercase tracking-tight group-hover:text-indigo-600 transition-colors">{{ $invite->name }}</span>

                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $invite->company ?? 'Personal' }}</span>
                                    <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                    <span class="text-[8px] font-black text-indigo-400 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $invite->category }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <i class="far fa-envelope text-[10px] text-gray-300"></i>
                                    <span class="text-[10px] font-bold text-gray-500">{{ $invite->email ?? '-' }}</span>
                                    @if($invite->is_sent_email) <i class="fas fa-check-double text-[8px] text-blue-500"></i> @endif
                                </div>
                                <div class="flex items-center gap-2 text-nowrap">
                                    <i class="fab fa-whatsapp text-[10px] text-gray-300"></i>
                                    <span class="text-[10px] font-bold text-gray-500">{{ $invite->phone_number ?? '-' }}</span>
                                    @if($invite->is_sent_whatsapp) <i class="fas fa-check-double text-[8px] text-emerald-500"></i> @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($invite->status == 'pending')
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-amber-100">Waiting</span>
                            @elseif($invite->status == 'confirmed')
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-emerald-100">Confirmed</span>
                            @elseif($invite->status == 'represented')
                            <div class="flex flex-col items-center gap-1">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-blue-100">Represented</span>
                                @if(!empty($invite->representative_data))
                                <span class="text-[8px] font-bold text-blue-800 uppercase tracking-tighter truncate max-w-[120px]">{{ $invite->representative_data['name'] ?? '-' }}</span>
                                <span class="text-[7px] font-medium text-gray-400 uppercase tracking-widest italic">{{ $invite->representative_data['jabatan'] ?? 'Proxy' }}</span>
                                @endif
                            </div>
                            @else
                            <span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-red-100">Declined</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="sendEmail({{ $invite->id }})" wire:loading.attr="disabled" class="p-2.5 rounded-xl border {{ $invite->is_sent_email ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'bg-gray-50 text-gray-400 hover:bg-blue-600 hover:text-white' }} transition-all flex items-center justify-center min-w-[40px]" title="Send Email">
                                    <i wire:loading.remove wire:target="sendEmail({{ $invite->id }})" class="fas fa-paper-plane text-[10px]"></i>
                                    <i wire:loading wire:target="sendEmail({{ $invite->id }})" class="fas fa-circle-notch animate-spin text-[10px]"></i>
                                </button>

                                @php

                                $linkSurat = route('invitation.letter', $invite->uuid);
                                $linkKonfirmasi = route('invitation.confirm', $invite->uuid);
                                $template = $waTemplate ?: ($event->invitation_wa_template ?: 'Halo {name}, Link: {link_konfirmasi}');
                                $finalMsg = str_replace(['{name}', '{company}', '{jabatan}', '{event_name}', '{link_surat}', '{link_konfirmasi}'], [$invite->name, $invite->company ?? '', $invite->category ?? '', $event->name, $linkSurat, $linkKonfirmasi], $template);
                                $phone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $invite->phone_number));
                                $waUrl = "https://wa.me/" . $phone . "?text=" . rawurlencode($finalMsg);
                                @endphp
                                <a href="{{ $waUrl }}" target="_blank" onclick="@this.markWaSent({{ $invite->id }})" class="p-2.5 rounded-xl border {{ $invite->is_sent_whatsapp ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-100' : 'bg-gray-50 text-gray-400 hover:bg-emerald-600 hover:text-white' }} transition-all" title="Manual Send (WhatsApp Web)">
                                    <i class="fab fa-whatsapp text-xs"></i>
                                </a>

                                <button wire:click="sendWhatsAppDirect({{ $invite->id }})" wire:loading.attr="disabled" class="p-2.5 rounded-xl border bg-gray-50 text-gray-400 hover:bg-indigo-600 hover:text-white transition-all" title="System Send (Fonnte API)">
                                    <i class="fas fa-robot text-[10px]"></i>
                                </button>

                                @if($invite->submission)
                                <button @click="$dispatch('open-submission-modal', { 
                                    submission: {{ json_encode($invite->submission->data, JSON_HEX_QUOT | JSON_HEX_APOS) }}, 
                                    name: '{{ addslashes($invite->name) }}', 
                                    media: {{ json_encode($invite->submission->getMedia('attachments')->map(fn($m) => ['name' => $m->name, 'file_name' => $m->file_name, 'url' => $m->getUrl()]), JSON_HEX_QUOT | JSON_HEX_APOS) }} 
                                })" class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="View Response">
                                    <i class="fas fa-file-invoice text-[10px]"></i>
                                </button>
                                @endif

                                <a href="{{ route('admin.events.invitations.edit', [$event, $invite]) }}" wire:navigate class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-[#1a1235] hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>

                                <button wire:click="confirmDeleteInvitation({{ $invite->id }})" class="p-2.5 bg-gray-50 text-gray-400 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <i class="fas fa-user-secret text-6xl text-gray-100 mb-6 block"></i>
                            <h3 class="text-xl font-black text-gray-300 uppercase tracking-tighter">No invitations found.</h3>
                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-2">Upload an Excel file to add guests</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invitations->hasPages())
        <div class="p-8 border-t border-gray-50 bg-gray-50/30">
            {{ $invitations->links() }}
        </div>
        @endif
    </div>
</div>

{{-- PREVIEW MODAL --}}
<div x-data="{ 
            show: false,
            previewContent: '',
            previewHeader: ''
        }"
    x-on:open-preview.window="
            show = true;
            let content = $wire.get('letterBody');
            // Mocking variables for preview
            content = content.replace('{name}', 'John Doe, M.Kom');
            content = content.replace('{company}', 'PT. Teknologi Masa Depan');
            content = content.replace('{jabatan}', 'Chief Executive Officer');
            content = content.replace('{event_name}', '{{ $event->name }}');
            content = content.replace('{link_konfirmasi}', '<center><a href=\'#\' style=\'display:inline-block;background:#4f46e5;color:#ffffff;padding:12px 24px;text-decoration:none;border-radius:10px;font-weight:800;font-size:12px;text-transform:uppercase;letter-spacing:0.1em;\'>Konfirmasi Kehadiran</a></center>');
            previewContent = content;
            
            // Handle Header Image Preview
            const headerInput = document.getElementById('letter_header');
            if (headerInput && headerInput.files && headerInput.files[0]) {
                previewHeader = URL.createObjectURL(headerInput.files[0]);
            } else {
                previewHeader = '{{ $existingLetterHeader ? asset('storage/' . $existingLetterHeader) : '' }}';
            }
        "
    x-show="show"
    class="fixed inset-0 z-[100] overflow-y-auto"
    style="display: none;">
    <div class="fixed inset-0 bg-[#1a1235]/90 backdrop-blur-md transition-opacity" @click="show = false"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4 md:p-12">
        <div class="bg-gray-100 w-full max-w-[210mm] shadow-2xl rounded-sm overflow-hidden relative animate-bounce-in flex flex-col">
            {{-- Toolbar Modal --}}
            <div class="sticky top-0 z-50 bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between shadow-sm">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Live Letter Preview (A4 Format)</span>
                <button @click="show = false" class="text-gray-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Kertas A4 --}}
            <div class="bg-white w-full min-h-[297mm] relative p-0 overflow-hidden shadow-inner bg-top bg-no-repeat bg-contain"
                :style="'background-image: url(' + previewHeader + ')'">

                {{-- Spacer agar teks tidak menabrak header (Kop Surat) --}}
                <div class="w-full h-48 md:h-56"></div>

                {{-- Body Content --}}
                <div class="relative z-10 px-16 pb-12 prose max-w-none font-serif text-gray-800" x-html="previewContent"></div>
            </div>

            {{-- Footer Modal Info --}}
            <div class="p-6 bg-indigo-50 border-t border-indigo-100 text-center">
                <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest">Note: Variable values shown are examples for layout verification.</p>
            </div>
        </div>
    </div>
</div>

{{-- Add Quill Assets (Existing) --}}
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>


{{-- MODAL KONFIRMASI HAPUS KOP SURAT --}}
@if($confirmingHeaderDeletion)
<div class="fixed inset-0 z-[110] overflow-y-auto">
    <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity" wire:click="cancelDeleteHeader"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-bounce-in">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-trash-alt text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter mb-2">Hapus Kop Surat?</h3>
                <p class="text-gray-500 text-[11px] font-medium leading-relaxed px-6">Tindakan ini akan menghapus aset gambar kop surat secara permanen dari server. Anda harus mengunggah ulang jika ingin menggunakannya kembali.</p>
            </div>
            <div class="flex border-t border-gray-100">
                <button wire:click="cancelDeleteHeader" class="flex-1 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:bg-gray-50 transition-all">Batal</button>
                <button wire:click="deleteLetterHeader" class="flex-1 py-5 text-[10px] font-black text-white bg-rose-600 uppercase tracking-widest hover:bg-rose-700 transition-all">Ya, Hapus Aset</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- MODAL KONFIRMASI HAPUS UNDANGAN --}}
@if($confirmingInvitationDeletion)
<div class="fixed inset-0 z-[110] overflow-y-auto">
    <div class="fixed inset-0 bg-[#1a1235]/60 backdrop-blur-sm transition-opacity" wire:click="cancelDeleteInvitation"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-bounce-in border border-rose-100">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-minus text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter mb-2">Hapus Undangan?</h3>
                <p class="text-gray-500 text-[11px] font-medium leading-relaxed px-6">Anda akan menghapus tamu ini dari daftar undangan.</p>
                
                @if($registrationToDelete)
                <div class="mt-6 p-4 bg-amber-50 rounded-2xl border border-amber-100 text-left">
                    <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Terdeteksi Data Registrasi
                    </p>
                    <p class="text-[10px] text-amber-800 leading-tight">Tamu ini sudah melakukan registrasi/check-in. Apakah Anda ingin menghapus data registrasinya juga?</p>
                </div>
                @endif
            </div>
            <div class="flex flex-col border-t border-gray-100">
                @if($registrationToDelete)
                <button wire:click="deleteInvitation(true)" class="w-full py-5 text-[10px] font-black text-white bg-rose-600 uppercase tracking-widest hover:bg-rose-700 transition-all border-b border-rose-700">Hapus Undangan & Data Registrasi</button>
                <button wire:click="deleteInvitation(false)" class="w-full py-5 text-[10px] font-black text-rose-600 bg-white uppercase tracking-widest hover:bg-rose-50 transition-all border-b border-gray-100">Hapus Undangan Saja</button>
                @else
                <button wire:click="deleteInvitation(false)" class="w-full py-5 text-[10px] font-black text-white bg-rose-600 uppercase tracking-widest hover:bg-rose-700 transition-all">Ya, Hapus Tamu</button>
                @endif
                <button wire:click="cancelDeleteInvitation" class="w-full py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:bg-gray-50 transition-all">Batal</button>
            </div>
        </div>
    </div>
</div>
@endif

@if($showDuplicateModal)
<div class="fixed inset-0 z-[120] overflow-y-auto">
    <div class="fixed inset-0 bg-[#1a1235]/80 backdrop-blur-md transition-opacity" wire:click="cancelImport"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-bounce-in border border-amber-100 flex flex-col max-h-[90vh]">
            {{-- Modal Header --}}
            <div class="p-8 border-b border-gray-100 bg-amber-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-amber-500 shadow-sm border border-amber-100">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">Duplikasi Terdeteksi</h3>
                        <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mt-0.5 italic">Beberapa email sudah terdaftar dalam sistem</p>
                    </div>
                </div>
                <div class="px-4 py-2 bg-white rounded-xl border border-amber-100 text-[10px] font-black text-amber-600 uppercase tracking-widest">
                    {{ count($duplicateEmails) }} Duplikat
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-8 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/30">
                <p class="text-[11px] font-medium text-gray-500 leading-relaxed mb-6">Sistem mendeteksi bahwa email di bawah ini sudah ada di daftar undangan event ini atau duplikat di dalam file yang Anda unggah. Silakan tinjau daftar berikut:</p>
                
                <div class="space-y-3">
                    @foreach($duplicateEmails as $dup)
                    <div class="p-4 bg-white rounded-2xl border border-gray-100 flex items-center justify-between group hover:border-amber-200 transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-[10px] font-black text-gray-400 group-hover:bg-amber-50 group-hover:text-amber-600 transition-colors">
                                {{ $dup['row'] }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-[#1a1235] uppercase tracking-tight">{{ $dup['name'] }}</span>
                                <span class="text-[10px] font-bold text-gray-400">{{ $dup['email'] }}</span>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[8px] font-black uppercase tracking-widest border border-amber-100">Duplicate</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 p-6 bg-indigo-50 rounded-3xl border border-indigo-100 flex items-center gap-6">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100 shrink-0">
                        <i class="fas fa-info-circle text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-[11px] font-black text-indigo-900 uppercase tracking-widest mb-1">Opsi Lanjutkan</h4>
                        <p class="text-[10px] text-indigo-700/60 leading-relaxed font-bold uppercase tracking-widest">
                            Jika Anda memilih "Lanjutkan Import", sistem akan mengabaikan daftar di atas dan hanya mengimport <span class="text-indigo-600">{{ count($uniqueRowsToImport) }} data tamu unik</span> lainnya.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 bg-white border-t border-gray-100 flex items-center gap-4">
                <button wire:click="cancelImport" class="flex-1 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:bg-gray-50 rounded-2xl transition-all leading-none border border-gray-100">Batalkan Import</button>
                <button wire:click="confirmImportWithoutDuplicates" class="flex-1 py-5 text-[10px] font-black text-white bg-indigo-600 uppercase tracking-widest hover:bg-indigo-700 rounded-2xl transition-all shadow-xl shadow-indigo-100 leading-none">Lanjutkan Import ({{ count($uniqueRowsToImport) }} Tamu)</button>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    @keyframes bounceIn {
        0% {
            transform: scale(0.9);
            opacity: 0;
        }

        50% {
            transform: scale(1.05);
            opacity: 1;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .animate-bounce-in {
        animation: bounceIn 0.5s ease-out forwards;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    /* E-Letter Preview Optimization */
    .prose p {
        margin-top: 1px !important;
        margin-bottom: 1px !important;
        line-height: 1.4 !important;
        text-align: justify !important;
        font-size: 12px !important;
    }
    
    .prose h1, .prose h2, .prose h3 {
        margin-top: 1em !important;
        margin-bottom: 0.5em !important;
    }

    .prose ul, .prose ol {
        margin-top: 0.5em !important;
        margin-bottom: 0.5em !important;
    }
</style>

</div>
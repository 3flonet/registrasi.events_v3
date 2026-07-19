<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.events.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Communication Manager</span>
             </div>
             <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                 Message Templates for <span class="text-indigo-600">{{ $event->getTranslation('name', 'en') }}</span>
             </h1>
             <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage and deploy multi-channel broadcast templates to participants</p>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex flex-col items-end">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Registrations</span>
                <span class="text-xl font-black text-[#1a1235]">{{ $event->registrations_count }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- 2. Architect / Broadcast Panel --}}
        <div class="lg:col-span-5">
            @if($isBroadcasting)
                {{-- BROADCAST HUB --}}
                <div class="bg-white rounded-2xl shadow-xl border border-indigo-100 overflow-hidden sticky top-10 animate-fade-in">
                    <div class="bg-[#1a1235] p-6 flex justify-between items-center">
                         <h3 class="text-lg font-black text-white uppercase tracking-tighter flex items-center gap-3">
                             <i class="fas fa-satellite-dish text-indigo-400"></i>
                             Initiate Broadcast
                         </h3>
                        <button wire:click="cancelBroadcast" class="text-white/40 hover:text-white transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-8 space-y-8">
                         <div>
                             <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Selected Template</span>
                             <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 italic text-sm text-[#1a1235] font-medium">
                                 "{{ $templateToSend->subject }}"
                             </div>
                         </div>
 
                         <div class="space-y-4">
                             <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">1. Choose Communication Channel</span>
                             <div class="grid grid-cols-2 gap-3 mb-6">
                                 <label @class([
                                     'flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all cursor-pointer group',
                                     'border-indigo-600 bg-indigo-50 shadow-sm' => $broadcastType === 'email',
                                     'border-gray-100' => $broadcastType !== 'email'
                                 ])>
                                     <i @class(['fas fa-envelope text-lg', 'text-indigo-600' => $broadcastType === 'email', 'text-gray-300' => $broadcastType !== 'email'])></i>
                                     <span @class(['text-[9px] font-black uppercase tracking-widest', 'text-indigo-900' => $broadcastType === 'email', 'text-gray-400' => $broadcastType !== 'email'])>Email</span>
                                     <input type="radio" wire:model.live="broadcastType" value="email" class="hidden">
                                 </label>

                                 <label @class([
                                     'flex flex-col items-center gap-2 p-4 rounded-2xl border-2 transition-all cursor-pointer group',
                                     'border-emerald-500 bg-emerald-50 shadow-sm' => $broadcastType === 'whatsapp',
                                     'border-gray-100' => $broadcastType !== 'whatsapp'
                                 ]) @if(!$templateToSend->whatsapp_content) title="WhatsApp content is empty" @endif>
                                     <i @class(['fab fa-whatsapp text-lg', 'text-emerald-500' => $broadcastType === 'whatsapp', 'text-gray-300' => $broadcastType !== 'whatsapp'])></i>
                                     <span @class(['text-[9px] font-black uppercase tracking-widest', 'text-emerald-900' => $broadcastType === 'whatsapp', 'text-gray-400' => $broadcastType !== 'whatsapp'])>WhatsApp</span>
                                     <input type="radio" wire:model.live="broadcastType" value="whatsapp" class="hidden" @if(!$templateToSend->whatsapp_content) disabled @endif>
                                 </label>
                             </div>

                             <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">2. Sending Scope</span>
                             
                             <label class="flex items-center gap-4 p-5 rounded-2xl border border-gray-100 hover:border-indigo-200 cursor-pointer transition-all {{ $sendTarget === 'test' ? 'bg-indigo-50 border-indigo-300' : 'bg-white' }}">
                                 <input type="radio" wire:model.live="sendTarget" value="test" class="hidden">
                                 <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm">
                                     <i class="fas fa-vial text-xs {{ $sendTarget === 'test' ? 'text-indigo-600' : 'text-gray-300' }}"></i>
                                 </div>
                                 <div>
                                     <span class="text-[11px] font-black uppercase tracking-widest block {{ $sendTarget === 'test' ? 'text-indigo-900' : 'text-gray-500' }}">Experimental Test</span>
                                     <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">Send a sample to yourself</span>
                                 </div>
                             </label>
 
                             <label class="flex items-center gap-4 p-5 rounded-2xl border border-gray-100 hover:border-indigo-200 cursor-pointer transition-all {{ $sendTarget === 'all' ? 'bg-amber-50 border-amber-300' : 'bg-white' }}">
                                 <input type="radio" wire:model.live="sendTarget" value="all" class="hidden">
                                 <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm">
                                     <i class="fas fa-users text-xs {{ $sendTarget === 'all' ? 'text-amber-600' : 'text-gray-300' }}"></i>
                                 </div>
                                 <div>
                                     <span class="text-[11px] font-black uppercase tracking-widest block {{ $sendTarget === 'all' ? 'text-amber-900' : 'text-gray-500' }}">Mass Transmission</span>
                                     <span class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">Broadcast to all {{ $event->registrations_count }} registrants</span>
                                 </div>
                             </label>
                         </div>

                        @if($sendTarget === 'test')
                            <div class="animate-fade-in space-y-4">
                                @if($broadcastType === 'email')
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Test Email Address</label>
                                        <input type="email" wire:model="testEmail" 
                                                class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all" 
                                                placeholder="test@example.com">
                                        @error('testEmail') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @else
                                    <div>
                                        <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Test Phone Number (WhatsApp)</label>
                                        <input type="text" wire:model="testPhone" 
                                                class="w-full px-5 py-4 bg-emerald-50/30 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-emerald-500 transition-all" 
                                                placeholder="e.g. 08123456789">
                                        @error('testPhone') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                        <p class="mt-2 text-[8px] text-gray-400 font-medium uppercase tracking-widest italic">Ensure international format (e.g. 628... or 08...)</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-6 bg-amber-50 rounded-2xl border border-amber-100 animate-fade-in flex gap-4">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                                 <div>
                                     <h5 class="text-[10px] font-black text-amber-900 uppercase tracking-widest mb-1">Warning: Mass Email</h5>
                                     <p class="text-[9px] text-amber-700 font-bold leading-relaxed uppercase tracking-tight">You are about to send a mass email to {{ $event->registrations_count }} participants. Ensure the content is correct.</p>
                                 </div>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-gray-50 flex gap-3">
                             <button wire:click="cancelBroadcast" class="flex-1 py-4 bg-gray-100 text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 transition-all">
                                 Cancel
                             </button>
                             <button wire:click="initiateBroadcast" wire:loading.attr="disabled" class="flex-1 py-4 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                                 <span wire:loading.remove wire:target="initiateBroadcast">Deploy Transmission</span>
                                 <span wire:loading wire:target="initiateBroadcast" class="flex items-center gap-2 italic">
                                     <i class="fas fa-circle-notch animate-spin"></i> Processing...
                                 </span>
                             </button>
                        </div>
                    </div>
                </div>
            @else
                {{-- TEMPLATE ARCHITECT --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-10">
                    <div class="bg-[#1a1235] p-6 flex justify-between items-center">
                         <h3 class="text-lg font-black text-white uppercase tracking-tighter flex items-center gap-3">
                             <i class="fas {{ $editingTemplateId ? 'fa-edit text-amber-400' : 'fa-magic text-indigo-400' }}"></i>
                             {{ $editingTemplateId ? 'Edit Template' : 'Create New Template' }}
                         </h3>
                        @if($isConfiguring)
                            <button wire:click="closeConfig" class="text-white/40 hover:text-white transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>

                    <div class="p-8">
                         @if(!$isConfiguring)
                             <div class="py-20 text-center space-y-6">
                                 <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto text-indigo-300">
                                     <i class="far fa-envelope-open text-3xl"></i>
                                 </div>
                                 <div>
                                     <h4 class="text-xs font-black text-[#1a1235] uppercase tracking-[0.2em] mb-2">No Template Selected</h4>
                                     <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Select a template to edit or create a new one.</p>
                                 </div>
                                 <button wire:click="create" class="px-8 py-4 bg-[#1a1235] text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100">
                                     Create New Template
                                 </button>
                             </div>
                        @else
                            <form wire:submit.prevent="save" class="space-y-6">
                                 <div>
                                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Message Subject (Email)</label>
                                     <input type="text" wire:model="subject" 
                                            class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all" 
                                            placeholder="e.g. Registration Successful">
                                     @error('subject') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                 </div>

                                 <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between">
                                     <div>
                                         <span class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest block">Global Template</span>
                                         <span class="text-[8px] text-gray-400 font-bold uppercase tracking-widest">Available for all events</span>
                                     </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="is_global" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>

                                 <div>
                                     <select wire:model="category" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all">
                                         @foreach($availableCategories as $cat)
                                             <option value="{{ $cat->slug }}">
                                                 @php
                                                     $emoji = '';
                                                     if(str_contains($cat->icon, 'envelope')) $emoji = '✉️ ';
                                                     elseif(str_contains($cat->icon, 'bolt')) $emoji = '⚡ ';
                                                     elseif(str_contains($cat->icon, 'receipt')) $emoji = '🧾 ';
                                                     elseif(str_contains($cat->icon, 'clock')) $emoji = '⏰ ';
                                                     elseif(str_contains($cat->icon, 'award')) $emoji = '🎓 ';
                                                     elseif(str_contains($cat->icon, 'folder')) $emoji = '📁 ';
                                                     else $emoji = '📣 ';
                                                 @endphp
                                                 {{ $emoji }}{{ $cat->name }}
                                             </option>
                                         @endforeach
                                     </select>
                                     <p class="mt-2 text-[8px] text-gray-400 font-bold uppercase tracking-tight italic text-left">The category determines how and when the template is used by the system.</p>
                                </div>

                                 <div>
                                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email Banner (Image)</label>
                                    @if($existingBannerPath || $banner)
                                        <div class="relative group rounded-2xl overflow-hidden mb-4 aspect-[21/9] shadow-inner bg-gray-100">
                                            <img src="{{ $banner ? $banner->temporaryUrl() : asset('storage/' . $existingBannerPath) }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-[#1a1235]/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                                                <button type="button" onclick="document.getElementById('banner-upload').click()" class="p-3 bg-white text-indigo-600 rounded-xl hover:bg-indigo-50"><i class="fas fa-camera"></i></button>
                                            </div>
                                        </div>
                                    @else
                                         <div onclick="document.getElementById('banner-upload').click()" class="border-2 border-dashed border-gray-100 rounded-2xl p-8 text-center hover:bg-gray-50 transition-all cursor-pointer group">
                                             <i class="fas fa-cloud-upload-alt text-2xl text-gray-100 group-hover:text-indigo-200 mb-2"></i>
                                             <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest block">Upload Banner Image</span>
                                         </div>
                                    @endif
                                    <input type="file" id="banner-upload" wire:model="banner" class="hidden" accept="image/*">
                                    @error('banner') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div wire:ignore x-data="{ 
                                    content: @entangle('content'),
                                    initEditor() {
                                        ClassicEditor.create($refs.editor_container, {
                                            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
                                        })
                                        .then(editor => {
                                            editor.model.document.on('change:data', () => {
                                                this.content = editor.getData();
                                            });
                                            $wire.on('set-content', (event) => {
                                                editor.setData(event.content);
                                            });
                                            $wire.on('init-editor', () => {
                                                editor.setData('');
                                            });
                                        });
                                    }
                                }" x-init="initEditor()">
                                     <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Email Body Content</label>
                                    <div class="prose-editor text-left">
                                        <textarea x-ref="editor_container" class="hidden">{{ $content }}</textarea>
                                    </div>
                                </div>

                                {{-- WhatsApp Payload --}}
                                <div class="pt-6 border-t border-gray-100">
                                    <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <i class="fab fa-whatsapp"></i> WhatsApp Payload (Personal Message)
                                    </label>
                                    <textarea wire:model.defer="whatsapp_content" rows="6" class="w-full px-5 py-4 bg-emerald-50/30 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-emerald-500 transition-all placeholder-emerald-300" placeholder="Enter optimized WhatsApp msg..."></textarea>
                                    <p class="mt-2 text-[8px] text-gray-400 font-medium uppercase tracking-widest italic text-left">Clear and concise. Leave empty to auto-clean from HTML (not recommended).</p>
                                    @error('whatsapp_content') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                 {{-- Magic Library --}}
                                 <div class="p-6 bg-indigo-50 rounded-2xl border border-indigo-100">
                                     <div class="flex items-center justify-between mb-4">
                                         <h4 class="text-[9px] font-black uppercase tracking-widest text-indigo-600 flex items-center gap-2">
                                             <i class="fas fa-magic"></i> Magic Library
                                         </h4>
                                         <span class="px-2 py-0.5 bg-white text-indigo-500 text-[7px] font-black uppercase tracking-widest rounded shadow-sm border border-indigo-100">AI Optimized</span>
                                     </div>
                                     <div class="space-y-2">
                                         @foreach([
                                             'Professional Confirmation' => [
                                                 'icon' => 'fa-certificate',
                                                 'subject' => 'Registration Confirmed: {event_name}',
                                                 'email' => '<p>Dear {name},</p><p>We are thrilled to confirm your registration for <strong>{event_name}</strong>.</p><p>{event_instruction}</p><p>Regards,<br>Team {event_name}</p>',
                                                 'wa' => "Halo {name}, pendaftaran Anda untuk *{event_name}* sudah berhasil! ✅\n\n{event_instruction}\n\nLihat tiket Anda di sini: {link_ticket}\n\nSampai jumpa!"
                                             ],
                                             'Event Reminder' => [
                                                 'icon' => 'fa-clock',
                                                 'subject' => '⏰ REMINDER: {event_name} starts soon!',
                                                 'email' => '<p>Hi {name},</p><p>This is a friendly reminder that <strong>{event_name}</strong> is happening soon!</p><p><strong>Date:</strong> {date}<br><strong>Time:</strong> {time}</p><p>{event_instruction}</p><p>We look forward to seeing you there!</p>',
                                                 'wa' => "Halo {name}, jangan lupa ya! Event *{event_name}* akan segera dimulai.\n\n📅 {date}\n⏰ {time}\n\n{event_instruction}\n\nSampai jumpa di lokasi! 🚀"
                                             ],
                                             'Urgent Announcement' => [
                                                 'icon' => 'fa-bullhorn',
                                                 'subject' => 'IMPORTANT: Update regarding {event_name}',
                                                 'email' => '<p>Hello {name},</p><p>We have an important update regarding our upcoming event <strong>{event_name}</strong>.</p><p>Please take note of the following details...</p>',
                                                 'wa' => "PENGUMUMAN PENTING: Halo {name}, ada informasi terbaru terkait event *{event_name}*.\n\nMohon perhatikan detail berikut..."
                                             ],
                                             'Post-Event Survey' => [
                                                 'icon' => 'fa-star',
                                                 'subject' => 'We value your feedback: {event_name}',
                                                 'email' => '<p>Hi {name},</p><p>Thank you for attending <strong>{event_name}</strong>. We hope you enjoyed the experience!</p><p>Please share your thoughts with us...</p>',
                                                 'wa' => "Hi {name}, terima kasih sudah hadir di *{event_name}*! 🙏\n\nBagaimana kesan Anda? Mohon bantu isi survey singkat kami di sini..."
                                             ]
                                         ] as $title => $data)
                                         <button type="button" 
                                             @click="
                                                 $wire.set('subject', '{{ $data['subject'] }}');
                                                 $wire.set('whatsapp_content', '{{ addslashes($data['wa']) }}');
                                                 $dispatch('set-content', {content: '{{ addslashes($data['email']) }}'});
                                             "
                                             class="w-full flex items-center gap-3 p-3 bg-white hover:bg-indigo-600 hover:text-white rounded-xl transition-all border border-indigo-100 group text-left">
                                             <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                                 <i class="fas {{ $data['icon'] }} text-[10px] text-indigo-600 group-hover:text-white"></i>
                                             </div>
                                             <span class="text-[9px] font-black uppercase tracking-widest">{{ $title }}</span>
                                         </button>
                                         @endforeach
                                     </div>
                                 </div>

                                 {{-- Smart Placeholders Reference --}}
                                 <div class="p-6 bg-[#1a1235] rounded-2xl text-white shadow-xl shadow-indigo-100/20">
                                     <h4 class="text-[9px] font-black uppercase tracking-widest mb-4 flex items-center gap-2 text-left">
                                         <i class="fas fa-terminal text-indigo-400"></i> Smart Placeholders
                                     </h4>
                                     <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-left">
                                         @foreach([
                                             'Full Name' => '{name}',
                                             'Event Title' => '{event_name}',
                                             'Instruction' => '{event_instruction}',
                                             'Date' => '{date}',
                                             'Time' => '{time}',
                                             'Total Pay' => '{total_bayar}',
                                             'Certificate' => '{link_certificate}',
                                             'Sertifikat' => '[link_sertifikat]',
                                         ] as $lbl => $tag)
                                             <div class="flex flex-col">
                                                 <span class="text-[8px] font-black uppercase tracking-tight text-white/40 leading-none mb-1 text-left">{{ $lbl }}</span>
                                                 <code class="text-[10px] font-mono text-indigo-300 font-black tracking-tight text-left">{{ $tag }}</code>
                                             </div>
                                         @endforeach
                                     </div>
                                 </div>

                                <div class="pt-4 border-t border-gray-100 flex gap-3">
                                    <button type="button" wire:click="closeConfig" class="flex-1 py-4 bg-gray-50 text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all">
                                        Cancel
                                    </button>
                                     <button type="submit" class="flex-1 py-4 {{ $editingTemplateId ? 'bg-amber-500' : 'bg-[#1a1235]' }} text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition-all shadow-xl active:scale-95 flex items-center justify-center gap-2">
                                         <i class="fas {{ $editingTemplateId ? 'fa-check-double' : 'fa-plus-circle' }}"></i>
                                         {{ $editingTemplateId ? 'Save Changes' : 'Save Template' }}
                                     </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- 3. Archive & Analytics Panel --}}
        <div class="lg:col-span-7 space-y-8">
            @if (session()->has('message'))
                <div class="p-4 bg-green-50 rounded-2xl border-l-4 border-green-500 flex items-center gap-3 text-green-700 animate-fade-in shadow-sm">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest leading-none">{{ session('message') }}</span>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="p-4 bg-red-50 rounded-2xl border-l-4 border-red-500 flex items-center gap-3 text-red-700 animate-fade-in shadow-sm">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest leading-none">{{ session('error') }}</span>
                </div>
            @endif

            {{-- EXISTING TEMPLATES --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                     <div>
                                         <h2 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">Saved Templates</h2>
                                         <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">List of saved message templates</p>
                                     </div>
                    <span class="px-3 py-1 bg-white border border-gray-100 rounded-full text-[9px] font-black text-gray-400 uppercase tracking-widest shadow-sm">
                        {{ count($templates) }} Nodes
                    </span>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse ($templates as $template)
                        <div class="p-8 hover:bg-gray-50/50 transition-colors group">
                            <div class="flex items-center justify-between gap-6">
                                <div class="flex items-center gap-6">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-50 overflow-hidden shrink-0 shadow-inner border border-gray-100">
                                        @if($template->banner_path)
                                            <img src="{{ asset('storage/' . $template->banner_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-200"><i class="far fa-image"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            @if($template->whatsapp_content)
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase tracking-widest rounded border border-emerald-100">
                                                    <i class="fab fa-whatsapp"></i> WA Ready
                                                </span>
                                            @endif
                                            @php
                                                $catRecord = $availableCategories->where('slug', $template->category)->first();
                                                $color = $catRecord->color ?? 'gray';
                                                $icon = $catRecord->icon ?? 'fa-file-alt';
                                                $label = $catRecord->name ?? 'Template';
                                            @endphp
                                            <span class="px-2 py-0.5 bg-{{ $color }}-50 text-{{ $color }}-600 text-[8px] font-black uppercase tracking-widest rounded border border-{{ $color }}-100 flex items-center gap-1 w-fit">
                                                <i class="fas {{ $icon }}"></i> {{ $label }}
                                            </span>
                                            <h4 class="text-sm font-black text-[#1a1235] group-hover:text-indigo-600 transition-colors uppercase tracking-tight">{{ $template->subject }}</h4>
                                        </div>
                                        <div class="flex items-center gap-4 text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                            <span><i class="far fa-calendar-alt mr-1"></i> {{ $template->created_at->format('d M Y') }}</span>
                                            <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                            <span><i class="far fa-clock mr-1"></i> {{ $template->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                     @if($catRecord->is_manual_sendable ?? true)
                                         <button wire:click="openBroadcast({{ $template->id }})" class="p-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white shadow-sm transition-all text-xs" title="Send Multi-Channel Broadcast">
                                             <i class="fas fa-paper-plane"></i>
                                         </button>
                                     @endif
                                     <button wire:click="edit({{ $template->id }})" class="p-3 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white shadow-sm transition-all text-xs" title="Edit Template">
                                         <i class="far fa-edit"></i>
                                     </button>
                                     <button wire:click="confirmDelete({{ $template->id }})" class="p-3 bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white shadow-sm transition-all text-xs" title="Delete">
                                         <i class="far fa-trash-alt"></i>
                                     </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-20 text-center">
                            <i class="far fa-folder-open text-5xl text-gray-100 mb-4 block"></i>
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest italic">No narrative archives initialized.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- BROADCAST HISTORY --}}
            <div class="bg-[#1a1235] rounded-2xl shadow-xl overflow-hidden border border-white/5">
                <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/5">
                     <div>
                         <h2 class="text-xl font-black text-white uppercase tracking-tighter">Transmission History</h2>
                         <p class="text-[9px] text-white/40 font-bold uppercase tracking-widest mt-0.5">History of multi-channel broadcasts sent for this event</p>
                     </div>
                </div>

                <div class="divide-y divide-white/5">
                    @forelse ($broadcastHistory as $broadcast)
                        <div class="p-8 hover:bg-white/5 transition-all">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="space-y-3 flex-grow max-w-md">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[9px] font-black uppercase tracking-widest
                                            @switch($broadcast->status)
                                                @case('pending') text-amber-400 @break
                                                @case('processing') text-blue-400 @break
                                                @case('completed') text-green-400 @break
                                                @case('failed') text-red-400 @break
                                            @endswitch">
                                            ● {{ $broadcast->status }}
                                        </span>
                                        <h4 class="text-[11px] font-black text-white uppercase tracking-wider truncate">{{ $broadcast->template->subject ?? 'Deleted Protocol' }}</h4>
                                        @if($broadcast->type === 'whatsapp')
                                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[7px] font-black uppercase tracking-widest rounded border border-emerald-500/20 flex items-center gap-1 w-fit">
                                                <i class="fab fa-whatsapp"></i> WhatsApp
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 text-[7px] font-black uppercase tracking-widest rounded border border-indigo-500/20 flex items-center gap-1 w-fit">
                                                <i class="fas fa-envelope"></i> Email
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-6 text-[9px] font-bold text-white/30 uppercase tracking-widest">
                                         <span>Sent: {{ $broadcast->created_at->format('d M, H:i') }}</span>
                                        <span>Target: {{ $broadcast->total_recipients }} Nodes</span>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-3 shrink-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-32 bg-white/10 h-1.5 rounded-full overflow-hidden">
                                            @php
                                                $percentage = $broadcast->total_recipients > 0 ? ($broadcast->progress / $broadcast->total_recipients) * 100 : 0;
                                            @endphp
                                            <div class="bg-indigo-400 h-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-black text-white italic tracking-tighter">{{ round($percentage) }}%</span>
                                    </div>
                                    <span class="text-[9px] font-black text-white/60 tracking-[0.2em] uppercase leading-none">{{ $broadcast->progress }} / {{ $broadcast->total_recipients }} Complete</span>
                                </div>
                            </div>
                            @if($broadcast->status == 'failed')
                                <div class="mt-4 p-4 bg-red-500/10 rounded-xl border border-red-500/20 text-[9px] font-mono text-red-400 leading-relaxed italic">
                                     ERROR DETAILS: {{ $broadcast->error_message }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-20 text-center">
                            <p class="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Zero transmissions detected in logs.</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-6 bg-white/2">
                    {{ $broadcastHistory->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .prose-editor .ck-editor__editable {
            min-height: 300px;
            border-radius: 0 0 1.5rem 1.5rem !important;
            border: none !important;
            background-color: #f9fafb !important;
            padding: 2rem !important;
            font-family: inherit !important;
            font-size: 0.875rem !important;
        }
        .prose-editor .ck-toolbar {
            border-radius: 1.5rem 1.5rem 0 0 !important;
            border: none !important;
            background-color: #f3f4f6 !important;
            padding: 0.5rem 1rem !important;
        }
        .prose-editor .ck-focused {
            box-shadow: inset 0 0 0 2px #6366f1 !important;
            background-color: #fff !important;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    </style>

    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[110] overflow-y-auto">
        <div class="fixed inset-0 bg-red-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showDeleteModal', false)"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white p-10 text-center shadow-2xl transition-all w-full max-w-md border border-gray-100">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-3xl bg-red-50 mb-8 text-red-500 shadow-inner">
                    <i class="far fa-trash-alt text-4xl animate-bounce"></i>
                </div>
                <h3 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">Erase Template?</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-10 leading-relaxed">This template will be permanently removed. Any pending broadcasts using this template might be affected.</p>
                <div class="flex gap-4">
                    <button wire:click="$set('showDeleteModal', false)" class="flex-1 py-4 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-100 transition-all leading-none">Cancel</button>
                    <button wire:click="delete" class="flex-1 py-4 bg-red-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-red-700 transition-all leading-none shadow-xl shadow-red-100 active:scale-95">Confirm Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('swal:success', (event) => {
                const data = event[0];
                Swal.fire({
                    icon: 'success',
                    title: data.title,
                    text: data.text,
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-2xl border-none shadow-2xl' }
                });
            });
        });
    </script>
    @endpush
</div>
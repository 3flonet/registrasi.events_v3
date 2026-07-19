<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.events.index') }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                 <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Registrant Management</span>
             </div>
             <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                 Registrants for <span class="text-indigo-600">{{ $event->name }}</span>
             </h1>
             <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage participants and registration data</p>
        </div>

        <div class="flex items-center gap-3">
             <button wire:click="openExportModal" class="px-6 py-4 bg-white text-[#1a1235] border border-gray-100 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                 <i class="fas fa-file-export text-indigo-500"></i> Export Attendees
             </button>
            <button wire:click="exportCheckinHistory" wire:loading.attr="disabled" class="px-6 py-4 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:opacity-90 transition-all shadow-xl shadow-indigo-100 flex items-center gap-2">
                <i class="fas fa-history text-indigo-400" wire:loading.remove wire:target="exportCheckinHistory"></i>
                <i class="fas fa-spinner animate-spin" wire:loading wire:target="exportCheckinHistory"></i>
                <span>Check-in Logs</span>
            </button>
        </div>
    </div>

     {{-- 2. Statistics Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        @foreach([
            ['label' => 'Total Registered', 'value' => $event->registrations_count, 'icon' => 'fa-users', 'color' => 'indigo'],
            ['label' => 'Checked-in (Selected Date)', 'value' => $registrants->filter(fn($r) => $r->checkinLogs->isNotEmpty())->count(), 'icon' => 'fa-user-check', 'color' => 'emerald'],
            ['label' => 'Invitations', 'value' => $event->registrations()->where('data->source', 'Invitation System')->count(), 'icon' => 'fa-paper-plane', 'color' => 'purple'],
            ['label' => 'Active Sessions', 'value' => $event->daily_schedules ? count($event->daily_schedules) : 0, 'icon' => 'fa-calendar-day', 'color' => 'amber']
        ] as $stat)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 flex items-center justify-center">
                    <i class="fas {{ $stat['icon'] }} text-lg"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">{{ $stat['label'] }}</span>
                    <span class="text-xl font-black text-[#1a1235]">{{ $stat['value'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="space-y-8">
        {{-- 3. Communications Hub (Broadcast) --}}
        <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="open = !open" class="w-full p-6 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-bullhorn text-sm"></i>
                    </div>
                     <div>
                         <h3 class="text-sm font-black text-[#1a1235] uppercase tracking-tight text-left">Broadcast Center</h3>
                         <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest text-left">Send email broadcasts to selected participants</p>
                     </div>
                </div>
                <i class="fas fa-chevron-down text-gray-300 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-collapse>
                <div class="p-8 pt-2 border-t border-gray-50 space-y-8">
                     @if($templates->isNotEmpty())
                         <div>
                             <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-3">Load Email Template</span>
                             <div class="flex flex-wrap gap-2">
                                 @foreach($templates as $template)
                                     <div class="group flex items-center bg-gray-50 border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest overflow-hidden hover:border-indigo-200 transition-all">
                                         <button wire:click="loadTemplate({{ $template->id }})" class="pl-4 pr-3 py-2.5 text-indigo-600 hover:bg-indigo-50 transition-colors">
                                             {{ $template->subject }}
                                         </button>
                                         <button wire:click="deleteTemplate({{ $template->id }})" wire:confirm="Delete this template?" class="px-3 py-2.5 text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors border-l border-gray-100">
                                             <i class="fas fa-times"></i>
                                         </button>
                                     </div>
                                 @endforeach
                             </div>
                         </div>
                     @endif

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-8 space-y-6">
                             @if(count($selectedRegistrants) > 0)
                                 <div class="px-6 py-4 bg-indigo-50 rounded-2xl border border-indigo-100 flex items-center justify-between">
                                     <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ count($selectedRegistrants) }} Selected Participants</span>
                                     <button wire:click="$set('selectedRegistrants', [])" class="text-[9px] font-black text-indigo-400 hover:text-red-500 uppercase tracking-widest">Reset Selection</button>
                                 </div>
                             @endif
 
                             <div>
                                 <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Broadcast Subject</label>
                                 <input type="text" wire:model.defer="broadcastSubject" 
                                        class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-[#1a1235] focus:ring-2 focus:ring-indigo-500 transition-all" 
                                        placeholder="e.g. Event Schedule Update">
                             </div>

                            <div wire:ignore x-data="{ init() { 
                                ClassicEditor.create(this.$refs.editor).then(editor => {
                                    editor.model.document.on('change:data', () => { @this.set('broadcastContent', editor.getData()); });
                                    Livewire.on('template-loaded', (e) => { editor.setData(e.detail.content); });
                                });
                            }}">
                                 <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Email Message</label>
                                 <div class="prose-editor">
                                     <textarea x-ref="editor">{{ $broadcastContent }}</textarea>
                                 </div>
                            </div>
                        </div>

                        <div class="lg:col-span-4 space-y-6">
                            <div class="p-6 bg-[#1a1235] rounded-2xl text-white">
                                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                    <i class="fas fa-code text-indigo-400"></i> Placeholders
                                </h4>
                                <div class="space-y-3">
                                     @php
                                         $broadcastTags = [
                                             '[nama_peserta]' => 'Participant Name',
                                             '[nama_event]' => 'Event Name',
                                             '[tombol_aksi]' => 'Call to Action Button',
                                             '[link_e_tiket]' => 'E-ticket Link'
                                         ];
                                         if($event->certificate_config['is_active'] ?? false) {
                                             $broadcastTags['[link_sertifikat]'] = 'Certificate Link';
                                             $broadcastTags['{link_certificate}'] = 'Certificate Link';
                                         }
                                     @endphp
                                    @foreach($broadcastTags as $tag => $desc)
                                        <div class="group cursor-pointer" onclick="navigator.clipboard.writeText('{{ $tag }}'); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Copied {{ $tag }}', showConfirmButton: false, timer: 1500, background: '#1a1235', color: '#fff' })">
                                            <div class="flex justify-between items-center mb-1">
                                                <code class="text-[10px] font-mono text-indigo-300 font-bold">{{ $tag }}</code>
                                                <i class="fas fa-copy text-[8px] text-white/20 group-hover:text-white/60 transition-colors"></i>
                                            </div>
                                            <span class="text-[8px] font-black text-white/30 uppercase tracking-widest">{{ $desc }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="p-6 bg-gray-50 rounded-2xl space-y-4">
                                 <button wire:click="sendBroadcast" class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest hover:opacity-90 transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-3">
                                     <i class="fas fa-paper-plane"></i> Send Broadcast Now
                                 </button>
                                 <button wire:click="saveTemplate" class="w-full py-5 bg-white text-[#1a1235] border border-gray-100 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-gray-100 transition-all shadow-sm">
                                     Save as Template
                                 </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Command Center / List --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-gray-50/50">
                <div class="flex items-center gap-6 flex-grow">
                     <div class="relative w-full lg:w-96 group">
                         <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-indigo-500 transition-colors"></i>
                         <input type="text" wire:model.live.debounce.300ms="search" 
                                class="w-full pl-12 pr-4 py-4 bg-white border-none rounded-2xl text-[10px] font-medium uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm" 
                                placeholder="Search registrant by name or email...">
                     </div>
                     
                     <select wire:model.live="filterType" class="px-6 py-4 bg-white border-none rounded-2xl text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm cursor-pointer">
                         <option value="all">All Registrants</option>
                         @if($event->is_paid_event)
                            <option value="paid" class="text-emerald-600">✅ Success (Paid)</option>
                            <option value="unpaid" class="text-amber-600">⏳ Pending (Unpaid)</option>
                         @endif
                         <option value="regular">General Registrants (Regular)</option>
                         <option value="invited">VIP Registrants (Invited)</option>
                     </select>

                    <div class="h-10 w-px bg-gray-200 hidden lg:block mx-2"></div>

                     <div class="flex items-center gap-3">
                         <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Filter by Date:</span>
                         <input type="date" wire:model.live="selectedDate" 
                                class="px-4 py-3 bg-white border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm cursor-pointer shadow-indigo-50">
                     </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-8 py-5 w-10">
                                <input type="checkbox" wire:model.live="selectAll" class="w-5 h-5 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500">
                            </th>
                             <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">Participant Name</th>
                             <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50">Registration Info</th>
                             <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50 text-center">Check-in Status</th>
                             <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-50 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($registrants as $registration)
                            <tr class="hover:bg-indigo-50/20 transition-all group">
                                <td class="px-8 py-6">
                                    <input type="checkbox" wire:model.live="selectedRegistrants" value="{{ $registration->id }}" class="w-5 h-5 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-[#1a1235] font-black text-xs border border-gray-100 uppercase group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                            {{ substr($registration->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <button wire:click="showDetails({{ $registration->id }})" class="text-sm font-black text-[#1a1235] uppercase tracking-tight hover:text-indigo-600 transition-colors">{{ $registration->name }}</button>
                                                @if(($registration->data['source'] ?? '') === 'Invitation System')
                                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-600 text-[8px] font-black rounded-lg uppercase tracking-widest border border-purple-100">VIP</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-3 text-[10px] font-medium text-gray-400">
                                                <span class="flex items-center gap-1.5"><i class="far fa-envelope text-[8px]"></i> {{ $registration->email }}</span>
                                                <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                                <span class="flex items-center gap-1.5"><i class="fas fa-phone-alt text-[8px]"></i> {{ $registration->phone_number ?? '-' }}</span>
                                                @if($event->is_paid_event)
                                                    <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                                                    @if($registration->payment_status === 'paid')
                                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[8px] font-black rounded-lg uppercase tracking-widest border border-emerald-100">Paid</span>
                                                    @else
                                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[8px] font-black rounded-lg uppercase tracking-widest border border-amber-100">Unpaid</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                     <div class="text-[10px] font-black text-[#1a1235] uppercase tracking-widest mb-1">Registered:</div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $registration->created_at->format('d M Y, H:i') }}</div>
                                    @if(isset($registration->data['representing']))
                                        <div class="mt-2 flex items-center gap-1.5">
                                            <i class="fas fa-reply rotate-180 text-[10px] text-indigo-300"></i>
                                            <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest italic">Representing: {{ $registration->data['representing'] }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                         @if($registration->checkinLogs->isNotEmpty())
                                             <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[8px] font-black rounded-full uppercase tracking-widest border border-emerald-100">Checked-in</span>
                                         @else
                                             <span class="px-3 py-1 bg-gray-50 text-gray-400 text-[8px] font-black rounded-full uppercase tracking-widest border border-gray-100">Not Checked-in</span>
                                         @endif
                                         
                                        @if($registration->checkinLogs->isNotEmpty())
                                            <div class="flex gap-1">
                                                @foreach($registration->checkinLogs->sortByDesc('checkin_time')->take(3) as $log)
                                                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400" title="{{ \Carbon\Carbon::parse($log->checkin_time)->format('d M') }}"></div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2 text-nowrap">
                                        @php $isCheckedIn = $registration->checkinLogs->isNotEmpty(); @endphp
                                        @if($event->certificate_config['is_active'] ?? false)
                                            @if($isCheckedIn || $registration->checked_in_at)
                                                <a href="{{ route('public.certificate.show', $registration->uuid) }}" target="_blank" class="p-3 bg-amber-50 text-amber-600 rounded-2xl hover:bg-amber-600 hover:text-white transition-all shadow-sm" title="Download Certificate">
                                                    <i class="fas fa-certificate text-xs"></i>
                                                </a>
                                            @else
                                                <button disabled class="p-3 bg-gray-50 text-gray-300 rounded-2xl cursor-not-allowed shadow-sm" title="Certificate available after Check-in">
                                                    <i class="fas fa-certificate text-xs"></i>
                                                </button>
                                            @endif
                                        @endif
                                        <button wire:click="toggleCheckIn({{ $registration->id }})" class="p-3 {{ $isCheckedIn ? 'bg-emerald-600 text-white shadow-emerald-100' : 'bg-gray-50 text-gray-400' }} rounded-2xl hover:opacity-90 transition-all shadow-sm">
                                            <i class="fas {{ $isCheckedIn ? 'fa-check' : 'fa-fingerprint' }} text-xs"></i>
                                        </button>
                                        <button wire:click="resendTicket({{ $registration->id }})" wire:loading.attr="disabled" class="p-3 bg-blue-50 text-blue-600 rounded-2xl hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Resend Ticket Email">
                                            <i class="fas fa-paper-plane text-xs"></i>
                                        </button>
                                        @if($registration->phone_number)
                                            <button wire:click="resendWhatsapp({{ $registration->id }})" wire:loading.attr="disabled" class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Resend Ticket WhatsApp (Fonnte)">
                                                <i class="fab fa-whatsapp text-xs"></i>
                                            </button>
                                            <button wire:click="resendWhatsappWeb({{ $registration->id }})" wire:loading.attr="disabled" class="p-3 bg-teal-50 text-teal-600 rounded-2xl hover:bg-teal-600 hover:text-white transition-all shadow-sm" title="Resend Ticket WhatsApp (Web)">
                                                <i class="fab fa-whatsapp text-xs"></i> <i class="fas fa-external-link-alt text-[8px] ml-1"></i>
                                            </button>
                                            @if($event->reminder_template_id)
                                                <button wire:click="sendReminderWhatsappWeb({{ $registration->id }})" wire:loading.attr="disabled" class="p-3 bg-violet-50 text-violet-600 rounded-2xl hover:bg-violet-600 hover:text-white transition-all shadow-sm" title="Send Event Reminder (WhatsApp Web)">
                                                    <i class="fas fa-bell text-xs"></i> <i class="fab fa-whatsapp text-[8px] ml-0.5"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button disabled class="p-3 bg-gray-50 text-gray-300 rounded-2xl cursor-not-allowed shadow-sm" title="No Phone Number Available">
                                                <i class="fab fa-whatsapp text-xs"></i>
                                            </button>
                                            @if($event->reminder_template_id)
                                                <button disabled class="p-3 bg-gray-50 text-gray-300 rounded-2xl cursor-not-allowed shadow-sm" title="No Phone Number for Reminder">
                                                    <i class="fas fa-bell text-xs"></i>
                                                </button>
                                            @endif
                                        @endif
                                        <a href="{{ route('tickets.qrcode', $registration->uuid) }}" target="_blank" class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="View Digital Ticket">
                                            <i class="fas fa-qrcode text-xs"></i>
                                        </a>
                                         <button 
                                             onclick="Swal.fire({
                                                 title: 'Delete Participant?',
                                                 text: 'This action will permanently remove {{ $registration->name }} and all associated logs. This cannot be undone.',
                                                 icon: 'warning',
                                                 showCancelButton: true,
                                                 confirmButtonColor: '#ef4444',
                                                 cancelButtonColor: '#1a1235',
                                                 confirmButtonText: 'Yes, Delete Everything',
                                                 cancelButtonText: 'Abort',
                                                 background: '#ffffff',
                                                 borderRadius: '1.5rem',
                                                 customClass: {
                                                     title: 'text-sm font-black uppercase tracking-tighter text-[#1a1235]',
                                                     htmlContainer: 'text-[10px] font-bold uppercase tracking-widest text-gray-400',
                                                     confirmButton: 'px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest',
                                                     cancelButton: 'px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest'
                                                 }
                                             }).then((result) => {
                                                 if (result.isConfirmed) {
                                                     @this.destroyRegistration({{ $registration->id }})
                                                 }
                                             })"
                                             class="p-3 bg-red-50 text-red-400 rounded-2xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                             <i class="far fa-trash-alt text-xs"></i>
                                         </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                 <td colspan="5" class="py-24 text-center">
                                     <div class="flex flex-col items-center justify-center opacity-20">
                                         <i class="fas fa-users-slash text-6xl mb-4"></i>
                                         <p class="text-xs font-black uppercase tracking-[0.4em]">No registrants found</p>
                                     </div>
                                 </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-8 border-t border-gray-50 flex justify-center">
                {{ $registrants->links() }}
            </div>
    </div>
    
    {{-- Detail Modal Architecture --}}
    @if($showDetailModal && $selectedRegistrantForDetail)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-[#1a1235]/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden border border-white/20 animate-bounce-in">
                 <div class="bg-[#1a1235] p-10 flex justify-between items-start">
                     <div>
                         <span class="px-3 py-1 bg-indigo-500 text-white text-[9px] font-black uppercase tracking-widest rounded-lg mb-4 inline-block shadow-lg shadow-indigo-500/30">Participant Information</span>
                         <h3 class="text-3xl font-black text-white uppercase tracking-tighter leading-none">{{ $selectedRegistrantForDetail->name }}</h3>
                         <p class="text-indigo-300/60 text-[10px] font-black uppercase tracking-widest mt-2">Registration ID: #{{ $selectedRegistrantForDetail->uuid }}</p>
                     </div>
                     <button wire:click="closeDetailModal" class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white hover:bg-white/20 transition-all"><i class="fas fa-times"></i></button>
                 </div>

                <div class="p-10 max-h-[60vh] overflow-y-auto custom-scrollbar">
                     <div class="grid grid-cols-2 gap-8">
                         @foreach([
                             'Email Address' => $selectedRegistrantForDetail->email,
                             'Phone Number' => $selectedRegistrantForDetail->phone_number,
                             'Registered At' => $selectedRegistrantForDetail->created_at->format('d M Y, H:i'),
                             'Internal Reference' => $selectedRegistrantForDetail->id
                         ] as $lbl => $val)
                             <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-indigo-100 transition-all">
                                 <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">{{ $lbl }}</span>
                                 <span class="text-xs font-medium text-[#1a1235]">{{ $val ?: '-' }}</span>
                             </div>
                         @endforeach
                     </div>

                    @if(!empty($selectedRegistrantForDetail->data))
                        <div class="mt-10">
                         <h4 class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                 <span class="w-8 h-px bg-indigo-100"></span> Additional Data <span class="w-full h-px bg-indigo-100"></span>
                             </h4>
                            <div class="grid grid-cols-2 gap-6">
                                @foreach($selectedRegistrantForDetail->data as $key => $val)
                                    <div>
                                        <label class="block text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1.5">{{ str_replace('_', ' ', $key) }}</label>
                                        <p class="text-[11px] font-medium text-[#1a1235] bg-gray-50/50 p-4 rounded-2xl border border-gray-50">{{ is_array($val) ? implode(', ', $val) : $val }}</p>
                                    </div>
                             @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                 <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end">
                     <button wire:click="closeDetailModal" class="px-8 py-4 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition-all shadow-xl shadow-indigo-100">Close</button>
                 </div>
            </div>
        </div>
    @endif

    {{-- Export Protocol Modal --}}
    @if($showExportModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-[#1a1235]/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-bounce-in">
                 <div class="p-10 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                     <div>
                         <h3 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter">Export Settings</h3>
                         <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Select columns to export to Excel</p>
                     </div>
                     <button wire:click="closeExportModal" class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-300 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
                 </div>

                <div class="p-10 space-y-8">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($availableColumns as $key => $label)
                            <label class="group cursor-pointer">
                                <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="sr-only peer">
                                <div class="relative p-5 bg-white border-2 border-gray-100 rounded-[1.5rem] group-hover:border-indigo-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 transition-all flex flex-col items-center justify-center text-center h-full min-h-[100px] shadow-sm">
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-100 flex items-center justify-center peer-checked:bg-indigo-500 peer-checked:border-indigo-500 transition-all">
                                        <i class="fas fa-check text-[8px] text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 group-hover:text-indigo-600 peer-checked:text-indigo-600 transition-colors">{{ $label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                 <div class="p-10 bg-gray-50 border-t border-gray-100 flex gap-5">
                     <button wire:click="closeExportModal" class="px-8 py-5 bg-white border border-gray-100 text-gray-400 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:text-red-500 hover:border-red-100 transition-all shadow-sm">Cancel</button>
                     <button wire:click="exportSelected" class="flex-1 py-5 bg-[#1a1235] text-white text-[11px] font-black uppercase tracking-widest rounded-2xl hover:opacity-90 transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-3">
                         <i class="fas fa-file-excel text-indigo-400"></i> Export to Excel
                     </button>
                 </div>
            </div>
        </div>
    @endif

    <style>
        .prose-editor .ck-editor__editable {
            min-height: 200px;
            border-radius: 0 0 1.5rem 1.5rem !important;
            border: none !important;
            background-color: #f9fafb !important;
            padding: 1.5rem !important;
            font-size: 0.8rem !important;
        }
        .prose-editor .ck-toolbar {
            border-radius: 1.5rem 1.5rem 0 0 !important;
            border: none !important;
            background-color: #f3f4f6 !important;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes bounceIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .animate-bounce-in { animation: bounceIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        /* Custom Pagination Styling for Active Page */
        nav[role="navigation"] span[aria-current="page"] > span,
        nav[role="navigation"] button[aria-current="page"] {
            background-color: #4f46e5 !important; /* bg-indigo-600 */
            color: #ffffff !important;
            border-color: #4f46e5 !important;
            font-weight: 900 !important;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.4) !important;
            border-radius: 0.5rem !important;
            z-index: 10 !important;
        }
        nav[role="navigation"] a:hover, nav[role="navigation"] button:hover {
            background-color: #eef2ff !important; /* bg-indigo-50 */
            color: #4f46e5 !important;
            border-radius: 0.5rem !important;
        }
        nav[role="navigation"] span.relative.inline-flex, nav[role="navigation"] a.relative.inline-flex {
            border-radius: 0.5rem;
            margin: 0 2px;
        }
    </style>

    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

    <script>
        document.addEventListener('livewire:initialized', () => {
           Livewire.on('registration-deleted', (event) => {
               Swal.fire({
                   toast: true,
                   position: 'top-end',
                   icon: 'success',
                   title: event.message,
                   showConfirmButton: false,
                   timer: 3000,
                   timerProgressBar: true,
                   background: '#1a1235',
                   color: '#fff'
               });
           });

           Livewire.on('delete-failed', (event) => {
               Swal.fire({
                   icon: 'error',
                   title: 'Action Failed',
                   text: event.message,
                   confirmButtonColor: '#1a1235',
               });
           });
        });
    </script>
</div>
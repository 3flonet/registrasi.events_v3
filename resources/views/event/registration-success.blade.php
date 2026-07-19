<x-guest-layout 
    :title="$event->name" 
    :description="'Registration successful for ' . $event->name"
    :ogImage="$event->hasMedia('og_image') ? $event->getFirstMediaUrl('og_image') : ($event->hasMedia('default') ? $event->getFirstMediaUrl('default', 'card-banner') : null)"
>
    {{-- High-End Clean Aesthetic for Success Page --}}
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-16">
        <div class="max-w-xl w-full">
            {{-- Success Badge / Card --}}
            <div class="bg-white rounded-[3rem] shadow-[0_10px_50px_rgba(0,0,0,0.04)] border border-gray-50 overflow-hidden">
                <div class="p-10 md:p-14 text-center">
                    
                    {{-- Minimalist Checkmark --}}
                    <div class="mb-8 flex justify-center">
                        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center animate-bounce-in">
                            <i class="fas fa-check text-3xl text-emerald-500"></i>
                        </div>
                    </div>

                    <div class="space-y-4 mb-12">
                        <h2 class="text-3xl font-black text-[#1a1235] tracking-tight">
                            {{ __('messages.registration_successful') }}
                        </h2>
                        <p class="text-gray-400 font-medium leading-relaxed">
                            {{ __('messages.thank_you_for_registering', ['eventName' => $event->name]) }}
                        </p>
                    </div>

                    {{-- Conditional Details --}}
                    <div class="mb-12">
                        @if($registration->attendance_type == 'offline')
                            <div class="inline-flex items-center gap-3 px-6 py-3 bg-indigo-50 rounded-2xl border border-indigo-100/50">
                                <i class="fas fa-envelope-open-text text-indigo-500 text-sm"></i>
                                <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest">E-Ticket sent to your email</span>
                            </div>
                        @else
                            <div class="inline-flex items-center gap-3 px-6 py-3 bg-emerald-50 rounded-2xl border border-emerald-100/50">
                                <i class="fas fa-video text-emerald-500 text-sm"></i>
                                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest">Virtual access details sent</span>
                            </div>
                        @endif
                    </div>

                    {{-- Primary Actions --}}
                    <div class="grid grid-cols-1 gap-4 mb-8">
                        @if($registration->attendance_type == 'offline')
                            <a href="{{ route('tickets.qrcode', $registration->uuid) }}" target="_blank"
                               class="flex items-center justify-center gap-3 bg-[#1a1235] text-white py-5 px-8 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100">
                                <i class="fas fa-qrcode text-lg"></i>
                                Download E-Ticket
                            </a>
                        @elseif($registration->attendance_type == 'online' && $event->meeting_link)
                            <a href="{{ $event->meeting_link }}" target="_blank"
                               class="flex items-center justify-center gap-3 bg-emerald-600 text-white py-5 px-8 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100">
                                <i class="fas fa-external-link-alt text-lg"></i>
                                Join Virtual Room
                            </a>
                        @endif

                        @if($event->is_paid_event)
                            <a href="{{ url('/invoice/' . $registration->uuid) }}" target="_blank"
                               class="flex items-center justify-center gap-3 bg-gray-50 text-gray-400 py-5 px-8 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                                <i class="fas fa-file-invoice-dollar text-lg"></i>
                                View Invoice Details
                            </a>
                        @endif

                        {{-- WhatsApp Shortcut --}}
                        @php
                            $waNumber = config('fonnte.sender');
                            $waText = "TICKET_" . $registration->uuid;
                            $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $waNumber) . "?text=" . urlencode($waText);
                        @endphp
                        @if($waNumber)
                            <a href="{{ $waUrl }}" target="_blank"
                               class="flex items-center justify-center gap-3 text-emerald-600 py-5 px-8 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] border-2 border-transparent hover:bg-emerald-50 transition-all">
                                <i class="fab fa-whatsapp text-xl"></i>
                                Send to WhatsApp
                            </a>
                        @endif
                    </div>

                    {{-- NEW: Add to Calendar Section --}}
                    <div class="mt-8 pt-8 border-t border-gray-50">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.3em] mb-6">Add to your calendar</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <a href="{{ \App\Http\Controllers\Public\CalendarController::getGoogleLink($event) }}" target="_blank"
                               class="flex items-center justify-center gap-2 py-3 px-4 bg-white border border-gray-100 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-indigo-50 hover:border-indigo-100 transition-all shadow-sm">
                                <img src="https://www.gstatic.com/calendar/images/dynamiclogo_2020q4/calendar_31_2x.png" class="w-4 h-4">
                                Google
                            </a>
                            <a href="{{ \App\Http\Controllers\Public\CalendarController::getOutlookLink($event) }}" target="_blank"
                               class="flex items-center justify-center gap-2 py-3 px-4 bg-white border border-gray-100 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-indigo-50 hover:border-indigo-100 transition-all shadow-sm">
                                <i class="fab fa-microsoft text-blue-500"></i>
                                Outlook
                            </a>
                            <a href="{{ route('calendar.ics', $event->slug) }}"
                               class="flex items-center justify-center gap-2 py-3 px-4 bg-white border border-gray-100 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-indigo-50 hover:border-indigo-100 transition-all shadow-sm">
                                <i class="fas fa-apple-alt text-gray-800"></i>
                                iCal / Apple
                            </a>
                        </div>
                    </div>

                    {{-- NEW: Share to Social Section --}}
                    <div class="mt-8 pt-8 border-t border-gray-50">
                        <p class="text-[9px] font-black text-gray-300 uppercase tracking-[0.3em] mb-6 whitespace-nowrap">Invite your colleagues</p>
                        
                        @php
                            $eventUrl = route('events.show', $event->slug);
                            $shareText = "I'm attending '{$event->name}'! Check it out here: ";
                            $shareLinks = [
                                'whatsapp' => "https://wa.me/?text=" . urlencode($shareText . $eventUrl),
                                'twitter'  => "https://twitter.com/intent/tweet?text=" . urlencode($shareText) . "&url=" . urlencode($eventUrl),
                                'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($eventUrl),
                            ];
                        @endphp

                        <div class="flex justify-center items-center gap-4">
                            <a href="{{ $shareLinks['whatsapp'] }}" target="_blank" class="w-12 h-12 flex items-center justify-center bg-gray-50 text-emerald-500 rounded-xl hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="{{ $shareLinks['twitter'] }}" target="_blank" class="w-12 h-12 flex items-center justify-center bg-gray-50 text-black rounded-xl hover:bg-black hover:text-white transition-all shadow-sm">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            <a href="{{ $shareLinks['facebook'] }}" target="_blank" class="w-12 h-12 flex items-center justify-center bg-gray-50 text-blue-600 rounded-xl hover:bg-[#1877F2] hover:text-white transition-all shadow-sm">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Navigation --}}
                    <div class="mt-12 flex justify-center gap-8 border-t border-gray-50 pt-10">
                        <a href="{{ route('events.show', $event->slug) }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">Event Info</a>
                        <a href="{{ route('events.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-[#1a1235] transition-colors">More Events</a>
                    </div>
                </div>
            </div>

            {{-- Support Footer --}}
            <div class="mt-12 text-center opacity-40 hover:opacity-100 transition-opacity">
                 <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">© 2026 {{ config('app.name') }} • Support: support@registrasi.events</p>
            </div>
        </div>
    </div>

    <style>
        .animate-bounce-in {
            animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
    </style>
</x-guest-layout>
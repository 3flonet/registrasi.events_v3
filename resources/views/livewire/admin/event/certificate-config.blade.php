<div class="min-h-screen bg-[#f8fafc] p-6 lg:p-10">
    {{-- 1. Modern Header Studio --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.events.registrants', $event) }}" wire:navigate class="w-10 h-10 flex items-center justify-center bg-white rounded-xl shadow-sm hover:shadow-md transition-all text-gray-400 hover:text-indigo-600 group">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div class="h-8 w-[2px] bg-gray-200"></div>
                 <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Certificate Settings</span>
            </div>
            <div class="flex items-center gap-6">
                 <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter">
                     Certificate <span class="text-indigo-600">Designer</span>
                 </h1>
                
                {{-- Master Switch --}}
                 <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-2xl shadow-sm border border-gray-100">
                     <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Enable Certificate</span>
                    <button type="button" 
                            wire:click="$set('is_active', {{ $is_active ? 'false' : 'true' }})"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $is_active ? 'bg-indigo-600' : 'bg-gray-200' }}">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                    <span class="text-[10px] font-black {{ $is_active ? 'text-indigo-600' : 'text-gray-400' }} uppercase tracking-widest">{{ $is_active ? 'Live' : 'Draft' }}</span>
                </div>
            </div>
             <p class="text-gray-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Design the certificate for <span class="text-indigo-600 font-bold">{{ $event->name }}</span> award</p>
        </div>

        <div class="flex items-center gap-3">
             <button wire:click="save" class="px-8 py-4 bg-[#1a1235] text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:opacity-90 transition-all shadow-xl shadow-indigo-100 flex items-center gap-2">
                 <i class="fas fa-check-circle text-indigo-400"></i>
                 <span>Save Settings</span>
             </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        {{-- Left Column: Configuration Forms --}}
        <div class="lg:col-span-12 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                 <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                     <h3 class="text-sm font-black text-[#1a1235] uppercase tracking-tight">Layout Design</h3>
                     <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Customize the look of your digital certificate</p>
                 </div>
                
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        {{-- Background Image --}}
                        <div class="space-y-4">
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Background Image (A4 Landscape)</label>
                            <div class="relative group h-64 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 flex items-center justify-center overflow-hidden transition-all hover:border-indigo-200">
                                @if($bg_image)
                                    <img src="{{ $bg_image->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($existing_bg_url)
                                    <img src="{{ $existing_bg_url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-center p-6">
                                         <i class="fas fa-image text-3xl text-gray-200 mb-3 block"></i>
                                         <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No background uploaded.</span>
                                     </div>
                                @endif
                                
                                @if($bg_image || $existing_bg_url)
                                    <div class="absolute inset-0 bg-[#1a1235]/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-3">
                                         <label class="px-6 py-3 bg-white text-[#1a1235] rounded-xl font-black text-[10px] uppercase tracking-widest cursor-pointer hover:bg-indigo-50 transition-colors shadow-xl">
                                             <i class="fas fa-sync-alt mr-2"></i> Change Background
                                             <input type="file" wire:model="bg_image" class="hidden">
                                         </label>
                                        <span class="text-white font-black text-[8px] uppercase tracking-widest bg-[#1a1235]/60 px-2 py-1 rounded">Max 5MB • JPG/PNG</span>
                                    </div>
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                         <label class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest cursor-pointer hover:bg-indigo-700 transition-colors shadow-xl">
                                             <i class="fas fa-plus-circle mr-2"></i> Choose Background
                                             <input type="file" wire:model="bg_image" class="hidden">
                                         </label>
                                    </div>
                                @endif
                            </div>
                             <div wire:loading wire:target="bg_image" class="text-[9px] font-black text-indigo-600 uppercase">Uploading...</div>
                        </div>

                        {{-- Signature Asset --}}
                        <div class="space-y-4">
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Digital Signature (PNG Preferred)</label>
                            <div class="relative group h-64 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100 flex items-center justify-center overflow-hidden transition-all hover:border-indigo-200">
                                @if($signature_image)
                                    <img src="{{ $signature_image->temporaryUrl() }}" class="w-full h-full object-contain p-10">
                                @elseif($existing_signature_url)
                                    <img src="{{ $existing_signature_url }}" class="w-full h-full object-contain p-10">
                                @else
                                    <div class="text-center p-6">
                                         <i class="fas fa-signature text-3xl text-gray-200 mb-3 block"></i>
                                         <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No signature uploaded.</span>
                                     </div>
                                @endif

                                @if($signature_image || $existing_signature_url)
                                    <div class="absolute inset-0 bg-[#1a1235]/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-3">
                                         <label class="px-6 py-3 bg-white text-[#1a1235] rounded-xl font-black text-[10px] uppercase tracking-widest cursor-pointer hover:bg-indigo-50 transition-colors shadow-xl">
                                             <i class="fas fa-sync-alt mr-2"></i> Change Signature
                                             <input type="file" wire:model="signature_image" class="hidden">
                                         </label>
                                        <span class="text-white font-black text-[8px] uppercase tracking-widest bg-[#1a1235]/60 px-2 py-1 rounded">Transparent PNG Recommended</span>
                                    </div>
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                         <label class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest cursor-pointer hover:bg-indigo-700 transition-colors shadow-xl">
                                             <i class="fas fa-plus-circle mr-2"></i> Choose Signature
                                             <input type="file" wire:model="signature_image" class="hidden">
                                         </label>
                                    </div>
                                @endif
                            </div>
                             <div wire:loading wire:target="signature_image" class="text-[9px] font-black text-indigo-600 uppercase">Uploading...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Narrative Logic --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                     <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                         <h3 class="text-sm font-black text-[#1a1235] uppercase tracking-tight">Certificate Text</h3>
                         <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Set the title and message for the certificate</p>
                     </div>
                    <div class="p-8 space-y-6">
                        <div>
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Certificate Title</label>
                            <input type="text" wire:model.defer="title" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black text-[#1a1235] uppercase tracking-tight focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        <div>
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Certificate Message</label>
                            <textarea wire:model.defer="body" rows="4" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-medium text-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Authority Identity --}}
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                     <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                         <h3 class="text-sm font-black text-[#1a1235] uppercase tracking-tight">Signatory Details</h3>
                         <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Enter the name and title of the person signing the certificate</p>
                     </div>
                    <div class="p-8 space-y-6">
                        <div>
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Signer Name</label>
                            <input type="text" wire:model.defer="signer_name" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black text-[#1a1235] uppercase tracking-tight focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="e.g. Dr. Jane Doe, PhD">
                        </div>
                        <div>
                             <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Signer Title</label>
                            <input type="text" wire:model.defer="signer_title" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black text-gray-400 uppercase tracking-widest focus:ring-2 focus:ring-indigo-500 transition-all text-[11px]" placeholder="e.g. CHIEF EXECUTIVE ORCHESTRATOR">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section>
    <header class="mb-8">
        <h2 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">
            {{ __('Professional Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-400 font-medium uppercase tracking-widest text-[10px]">
            {{ __("Update your professional details and how we can contact you.") }}
        </p>
    </header>

    <form wire:submit="updateProfessionalProfile" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <div class="space-y-2">
                <x-input-label for="name" :value="__('Full Name')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                <x-text-input wire:model="name" id="name" type="text" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            {{-- Email --}}
            <div class="space-y-2">
                <x-input-label for="email" :value="__('Email Address')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                <x-text-input wire:model="email" id="email" type="email" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" required />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
        </div>

        @if(!$isSuperAdmin)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Phone Number --}}
            <div class="space-y-2">
                <x-input-label for="phone_number" :value="__('Phone Number')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                <x-text-input wire:model="phone_number" id="phone_number" type="text" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" />
                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
            </div>

            {{-- WhatsApp --}}
            <div class="space-y-2">
                <x-input-label for="whatsapp" :value="__('WhatsApp Number')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                <x-text-input wire:model="whatsapp" id="whatsapp" type="text" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" />
                <x-input-error class="mt-2" :messages="$errors->get('whatsapp')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Institution Name --}}
            <div class="space-y-2">
                <x-input-label for="nama_instansi" :value="__('Institution Name')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                <x-text-input wire:model="nama_instansi" id="nama_instansi" type="text" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" />
                <x-input-error class="mt-2" :messages="$errors->get('nama_instansi')" />
            </div>

            {{-- Position --}}
            <div class="space-y-2">
                <x-input-label for="jabatan" :value="__('Job Position')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                <x-text-input wire:model="jabatan" id="jabatan" type="text" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" />
                <x-input-error class="mt-2" :messages="$errors->get('jabatan')" />
            </div>
        </div>

        {{-- Institution Type --}}
        <div class="space-y-2">
            <x-input-label for="tipe_instansi" :value="__('Institution Category')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
            <select wire:model="tipe_instansi" id="tipe_instansi" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm py-2.5">
                <option value="">{{ __('Choose Category') }}</option>
                <option value="Government">Government</option>
                <option value="Company">Company</option>
                <option value="Association">Association</option>
                <option value="NGOs">NGOs</option>
                <option value="Academic">Academic</option>
                <option value="Media">Media</option>
                <option value="Other">Other</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('tipe_instansi')" />
        </div>

        {{-- Address --}}
        <div class="space-y-2">
            <x-input-label for="alamat" :value="__('Full Address')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
            <textarea wire:model="alamat" id="alamat" rows="3" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
        </div>
        @endif

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-8 py-3 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-lg shadow-indigo-100">
                {{ __('Update Profile') }}
            </button>

            <x-action-message class="text-sm text-emerald-600 font-bold" on="profile-updated">
                <i class="fas fa-check-circle mr-1"></i> {{ __('Changes saved successfully.') }}
            </x-action-message>
        </div>
    </form>
</section>

<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header class="mb-6">
        <h2 class="text-xl font-black text-[#1a1235] uppercase tracking-tighter">
            {{ __('Update Password') }}
        </h2>
        <p class="mt-1 text-sm text-gray-400 font-medium uppercase tracking-widest text-[10px]">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div class="space-y-2">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-gray-50/50 border-gray-100 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition-all" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="w-full px-6 py-3 bg-[#1a1235] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 transition-all shadow-lg shadow-indigo-100">
                {{ __('Update Password') }}
            </button>
        </div>
        
        <x-action-message class="text-sm text-emerald-600 font-bold block text-center" on="password-updated">
            <i class="fas fa-check-circle mr-1"></i> {{ __('Password updated successfully.') }}
        </x-action-message>
    </form>
</section>

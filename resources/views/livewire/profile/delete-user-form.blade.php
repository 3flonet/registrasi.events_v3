<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header class="mb-6">
        <h2 class="text-xl font-black text-rose-600 uppercase tracking-tighter">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-sm text-gray-400 font-medium uppercase tracking-widest text-[10px]">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="w-full px-6 py-3 bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-rose-600 hover:text-white transition-all border border-rose-100"
    >
        {{ __('Delete My Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-8 bg-white">
            <h2 class="text-2xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">
                {{ __('Confirm Deletion') }}
            </h2>

            <p class="text-sm text-gray-400 font-medium leading-relaxed mb-8">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="space-y-2">
                <x-input-label for="password" value="{{ __('Password') }}" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" />
                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full bg-gray-50 border-gray-100 rounded-xl focus:ring-rose-500 focus:border-rose-500 transition-all"
                    placeholder="{{ __('Enter your password') }}"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-10 flex justify-end gap-4">
                <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-100 transition-all">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="px-6 py-3 bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-rose-700 transition-all shadow-lg shadow-rose-100">
                    {{ __('Permanently Delete') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

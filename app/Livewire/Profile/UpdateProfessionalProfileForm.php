<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdateProfessionalProfileForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $whatsapp = '';
    public string $nama_instansi = '';
    public string $tipe_instansi = '';
    public string $jabatan = '';
    public string $alamat = '';

    public bool $isSuperAdmin = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->isSuperAdmin = $user->isSuperAdmin();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        
        if (!$this->isSuperAdmin) {
            $this->phone_number = $user->phone_number ?? '';
            $this->whatsapp = $user->whatsapp ?? '';
            $this->nama_instansi = $user->nama_instansi ?? '';
            $this->tipe_instansi = $user->tipe_instansi ?? '';
            $this->jabatan = $user->jabatan ?? '';
            $this->alamat = $user->alamat ?? '';
        }
    }

    /**
     * Update the professional profile information.
     */
    public function updateProfessionalProfile(): void
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ];

        if (!$this->isSuperAdmin) {
            $rules = array_merge($rules, [
                'phone_number' => ['nullable', 'string', 'max:20'],
                'whatsapp' => ['nullable', 'string', 'max:20'],
                'nama_instansi' => ['nullable', 'string', 'max:255'],
                'tipe_instansi' => ['nullable', 'string', 'max:255'],
                'jabatan' => ['nullable', 'string', 'max:255'],
                'alamat' => ['nullable', 'string', 'max:500'],
            ]);
        }

        $validated = $this->validate($rules);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function render()
    {
        return view('livewire.profile.update-professional-profile-form');
    }
}

<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Services\WhatsAppService;


class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public $user_id;
    public $name, $email;
    // public $allRoles; // Moved to render()
    public $assignedRoles = [];
    public $organizers = [];
    public $selectedOrganizerId;
    public $showModal = false;

    // === Create Staff Properties ===
    public $showCreateModal = false;
    public $newName = '';
    public $newEmail = '';
    // public $newPassword = ''; // Removed as per request, now auto-generated
    public $newRole = '';
    public $newWhatsapp = '';
    // public $availableRoles = []; // Moved to render()

    public function mount()
    {
        if (auth()->user()->isSuperAdmin()) {
            $this->organizers = \App\Models\Organizer::all();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        // If not super admin, only show users who have roles (Staff/Team) 
        // to avoid cluttering with thousands of event attendees/visitors
        if (!auth()->user()->isSuperAdmin()) {
            $query->whereHas('roles');
        }

        $users = $query->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->with('roles')
            ->latest()
            ->paginate(15);

        $allRoles = Role::all();
        $availableRoles = auth()->user()->isSuperAdmin() 
            ? $allRoles 
            : Role::whereNotIn('name', ['Super Admin'])->get();

        return view('livewire.admin.user.index', [
            'users' => $users,
            'allRoles' => $allRoles,
            'availableRoles' => $availableRoles
        ])
            ->layout('layouts.app');
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->newName = '';
        $this->newEmail = '';
        $this->newPassword = '';
        $this->newRole = '';
        $this->newWhatsapp = '';
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function createStaff()
    {
        $this->validate([
            'newName'      => 'required|string|min:2|max:255',
            'newEmail'     => 'required|email|unique:users,email',
            'newRole'      => 'required|exists:roles,name',
            'newWhatsapp'  => 'nullable|string|max:20',
        ], [
            'newName.required'     => 'Name is required.',
            'newEmail.required'    => 'Email is required.',
            'newEmail.unique'      => 'This email is already registered.',
            'newRole.required'     => 'Please assign a role.',
        ]);

        // Auto-generate a secure random password
        $generatedPassword = Str::random(12);

        $user = \App\Models\User::create([
            'name'         => $this->newName,
            'email'        => $this->newEmail,
            'password'     => bcrypt($generatedPassword),
            'phone_number' => $this->newWhatsapp ?: null,
            'whatsapp'     => $this->newWhatsapp ?: null,
            'organizer_id' => auth()->user()->isSuperAdmin()
                ? null
                : auth()->user()->organizer_id,
        ]);

        $user->assignRole($this->newRole);

        $this->closeCreateModal();
        session()->flash('message', "Staff member '{$user->name}' created successfully!");
    }

    public function sendCredentialsByEmail($userId)
    {
        $user = User::findOrFail($userId);

        if (!$user->email) {
            session()->flash('error', 'User does not have an email address.');
            return;
        }

        // Generate a new temporary password
        $tempPassword = Str::random(10);
        $user->password = bcrypt($tempPassword);
        $user->save();

        $appName = config('app.name', 'Platform');
        $loginUrl = route('login');

        try {
            Mail::raw(
                "Halo {$user->name},\n\n" .
                "Akun Anda di platform {$appName} telah dibuat / direset.\n\n" .
                "Detail Login:\n" .
                "Email   : {$user->email}\n" .
                "Password: {$tempPassword}\n\n" .
                "Login di: {$loginUrl}\n\n" .
                "Segera ganti password Anda setelah login pertama.\n\n" .
                "Salam,\n{$appName}",
                function ($message) use ($user, $appName) {
                    $message->to($user->email)
                        ->subject("[{$appName}] Informasi Akun Staff Anda");
                }
            );

            session()->flash('message', "✅ Credentials sent to {$user->email} via Email.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function sendCredentialsByWa($userId)
    {
        $user = User::findOrFail($userId);

        $phone = $user->phone_number ?? $user->whatsapp ?? null;

        if (!$phone) {
            session()->flash('error', "User '{$user->name}' does not have a phone/WhatsApp number registered.");
            return;
        }

        // Generate a new temporary password
        $tempPassword = Str::random(10);
        $user->password = bcrypt($tempPassword);
        $user->save();

        $appName  = config('app.name', 'Platform');
        $loginUrl = route('login');

        $message =
            "🔐 *Informasi Akun Staff - {$appName}*\n\n" .
            "Halo *{$user->name}*,\n" .
            "Berikut adalah detail login akun Anda:\n\n" .
            "📧 *Email*   : {$user->email}\n" .
            "🔑 *Password*: `{$tempPassword}`\n\n" .
            "🔗 *Login URL*:\n{$loginUrl}\n\n" .
            "⚠️ _Segera ganti password Anda setelah login pertama._\n\n" .
            "_— {$appName}_";

        $wa = app(WhatsAppService::class);
        $result = $wa->sendMessage($phone, $message);

        if ($result['status'] ?? false) {
            session()->flash('message', "✅ Credentials sent to {$phone} via WhatsApp.");
        } else {
            session()->flash('error', 'WhatsApp failed: ' . ($result['reason'] ?? 'Unknown error. Check Fonnte token.'));
        }
    }

    public function edit($id)
    {
        if ($id == 1) {
            return; // Hentikan eksekusi jika user ID adalah 1
        }

        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedOrganizerId = $user->organizer_id;
        $this->assignedRoles = $user->getRoleNames()->toArray();
        $this->showModal = true;
    }

    public function updateUserRoles()
    {
        $user = User::findOrFail($this->user_id);
        
        // Update Organizer if Super Admin
        if (auth()->user()->isSuperAdmin()) {
            $user->organizer_id = $this->selectedOrganizerId ?: null;
            $user->save();
        }

        $user->syncRoles($this->assignedRoles);

        $this->closeModal();
        session()->flash('message', 'User roles updated successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->user_id = null;
        $this->name = '';
        $this->email = '';
        $this->assignedRoles = [];
    }

    #[On('delete-user')]
    public function destroy($userId)
    {
        // Pengecekan keamanan agar user utama (ID 1) tidak bisa dihapus.
        if ($userId == 1) {
            $this->dispatch('delete-failed', message: 'Error: The main administrator cannot be deleted.');
            return;
        }

        // Cari dan hapus user
        if ($user = User::find($userId)) {
            $user->delete();
            // Kirim event sukses kembali ke browser
            $this->dispatch('user-deleted', message: 'User has been successfully deleted!');
        } else {
            // Kirim event gagal jika user tidak ditemukan
            $this->dispatch('delete-failed', message: 'Error: User not found.');
        }
    }
}

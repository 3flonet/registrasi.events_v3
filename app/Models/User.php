<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Registration;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\Mail\ResetPasswordMail;
use Exception;



use App\Traits\BelongsToOrganizer;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, BelongsToOrganizer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',      // <-- Tambahkan ini
        'nama_instansi',     // <-- Tambahkan ini
        'booth_number',      // <-- Tambahkan ini
        'tipe_instansi',     // <-- Tambahkan ini
        'phone_instansi',    // <-- Tambahkan ini
        'whatsapp',          // <-- Tambahkan ini
        'jabatan',           // <-- Tambahkan ini
        'alamat',            // <-- Tambahkan ini
        'tanda_tangan',      // <-- Tambahkan ini
        'description',
        'logo_path',
        'document_path',
        'website',
        'linkedin',
        'instagram',
        'facebook',
        'youtube_link',
        'document_link',
        'profile_data',
        'rfid_tag',
        'organizer_id',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_data' => 'array',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi untuk mengambil daftar peserta (attendees) yang telah di-scan oleh exhibitor.
     */
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'exhibitor_attendee', 'exhibitor_id', 'attendee_id')
            ->withTimestamps();
    }

    /**
     * Relasi untuk mengambil daftar exhibitor yang telah dikunjungi oleh peserta.
     */
    public function visitedExhibitors()
    {
        return $this->belongsToMany(User::class, 'exhibitor_attendee', 'attendee_id', 'exhibitor_id')
            ->withTimestamps();
    }

    public function favoritedExhibitors()
    {
        return $this->belongsToMany(User::class, 'favorite_exhibitors', 'user_id', 'exhibitor_id')
            ->withPivot('rating', 'is_loved') // <-- TAMBAHKAN INI
            ->withTimestamps();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    // Relasi ke Tenant Profile (Jika user ini adalah penjual)
    public function tenant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    // Relasi ke Order Barang (Sebagai pembeli)
    public function productOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductOrder::class);
    }

    // Riwayat Transaksi (Gabungan tiket & barang)
    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}

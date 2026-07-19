<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganizer;

class Setting extends Model
{
    use HasFactory, BelongsToOrganizer;

    protected $table = 'settings';

    protected $fillable = ['key', 'value', 'organizer_id'];
    
    public $timestamps = false;

    /**
     * Otomatis bersihkan cache saat ada perubahan data.
     */
    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('app_settings');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('app_settings');
        });
    }
}

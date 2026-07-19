<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;

class EventProgramme extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'link_url',
        'banner_path',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // UBAH RELASI JADI belongsToMany
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_programme_event');
    }
}
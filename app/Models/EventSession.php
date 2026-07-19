<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class EventSession extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'event_session_group_id',
        'event_agenda_id',
        'title',
        'description',
        'room_name',
        'start_time',
        'end_time',
        'quota',
        'is_checkin_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_checkin_active' => 'boolean',
        'quota' => 'integer',
        'event_agenda_id' => 'string',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(EventSessionGroup::class, 'event_session_group_id');
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(EventAgenda::class, 'event_agenda_id');
    }

    public function registrations(): BelongsToMany
    {
        return $this->belongsToMany(Registration::class, 'registration_session');
    }
}

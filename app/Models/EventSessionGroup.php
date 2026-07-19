<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSessionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'selection_type',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(EventSession::class)->orderBy('start_time', 'asc');
    }
}

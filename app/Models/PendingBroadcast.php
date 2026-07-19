<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToOrganizer;

class PendingBroadcast extends Model
{
    use HasFactory, BelongsToOrganizer;
    protected $fillable = ['template_id', 'status', 'type', 'target', 'organizer_id', 'total_count', 'processed_count'];

    /**
     * Definisikan relasi ke model EventEmailTemplate.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EventEmailTemplate::class, 'template_id');
    }
}
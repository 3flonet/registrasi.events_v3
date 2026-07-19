<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'name',
        'section_title',
        'section_description',
        'fields',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }
}

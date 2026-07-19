<?php

namespace App\Models;

use App\Models\InquirySubmission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use App\Traits\BelongsToOrganizer;

class InquiryForm extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia, BelongsToOrganizer;

    public $translatable = ['description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'fields',
        'notification_emails',
        'has_categories',
        'event_agenda_id',
        'organizer_id',
    ];

    protected $casts = [
        'fields' => 'array',
        'notification_emails' => 'array',
        'has_categories' => 'boolean',
    ];

    public function submissions()
    {
        return $this->hasMany(InquirySubmission::class);
    }

    public function categories()
    {
        return $this->hasMany(InquiryCategory::class)->orderBy('order')->orderBy('id');
    }

    public function agenda() 
    {
        return $this->belongsTo(EventAgenda::class, 'event_agenda_id');
    }

    // Untuk dropdown di user side jika tipe inquiry ini bisa untuk berbagai event
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}

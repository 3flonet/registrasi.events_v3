<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
// ▼▼▼ PASTIKAN ANDA MENGGUNAKAN IMPORT YANG BENAR INI ▼▼▼
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use App\Traits\BelongsToOrganizer;

class InquirySubmission extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, BelongsToOrganizer;

    protected $fillable = [
        'inquiry_form_id',
        'inquiry_category_id',
        'event_agenda_id',
        'organizer_id',
        'registration_id',
        'invitation_id',
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(InquiryForm::class, 'inquiry_form_id');
    }

    public function category()
    {
        return $this->belongsTo(InquiryCategory::class, 'inquiry_category_id');
    }

    public function agenda()
    {
        return $this->belongsTo(EventAgenda::class, 'event_agenda_id');
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }

    /**
     * Daftarkan konversi media (thumbnail).
     * Perhatikan type-hint `Media` di sini sekarang sudah benar.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(200)
              ->height(200)
              ->sharpen(10);
    }
}
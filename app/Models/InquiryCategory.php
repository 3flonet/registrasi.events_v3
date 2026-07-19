<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class InquiryCategory extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'inquiry_form_id',
        'name',
        'description',
        'price',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function form()
    {
        return $this->belongsTo(InquiryForm::class, 'inquiry_form_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_templates';

    protected $fillable = [
        'name',
        'category',
        'language_code',
        'body_preview',
        'parameters',
        'is_active',
        'meta_status',
        'meta_category',
        'rejected_reason',
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get templates by category.
     */
    public static function getByCategory($category)
    {
        return self::where('category', $category)
            ->where('is_active', true)
            ->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganizer;

class BroadcastTemplate extends Model
{
    use HasFactory, BelongsToOrganizer;

    protected $guarded = [];
}

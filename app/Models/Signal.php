<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    use HasFactory;

    protected $fillable = ['sequence','green_internal','yellow_internal'];

    protected $casts = ['sequence' => 'array'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatSlider extends Model
{
    use HasFactory;

    protected $fillable = [
        'images',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}

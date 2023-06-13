<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
        'image',
        'subcategory_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(SubCat::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OTP extends Model
{
    use HasFactory;

    protected $fillable = [
        'otp',
        'user_id',
        'expired_at',
    ];

    protected $hidden = [
        'otp',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

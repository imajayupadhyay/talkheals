<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSetting extends Model
{
    protected $fillable = [
        'admin_id',
        'session_duration',
        'buffer_time',
        'advance_booking_days',
        'timezone',
        'is_booking_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_booking_enabled' => 'boolean',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

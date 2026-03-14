<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'client_id',
        'admin_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'type',
        'session_type',
        'client_notes',
        'admin_notes',
        'cancelled_at',
        'cancellation_reason',
        'reminder_24h_sent',
        'reminder_12h_sent',
        'reminder_30min_sent',
        'google_meet_link',
        'google_event_id',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'cancelled_at' => 'datetime',
            'reminder_24h_sent' => 'boolean',
            'reminder_12h_sent' => 'boolean',
            'reminder_30min_sent' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

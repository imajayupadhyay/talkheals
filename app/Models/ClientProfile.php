<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProfile extends Model
{
    protected $fillable = [
        'client_id',
        'preferred_name',
        'pronouns',
        'date_of_birth',
        'gender_identity',
        'phone',
        'timezone',
        'occupation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'reason_for_therapy',
        'previous_therapy_experience',
        'current_medications',
        'mental_health_history',
    ];

    /**
     * Get the client that owns the profile.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}

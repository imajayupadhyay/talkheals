<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'title',
        'description',
        'workshop_date',
        'workshop_time',
        'duration_minutes',
        'image_url',
        'status',
        'zoom_link',
        'is_free',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'workshop_date' => 'date',
            'is_free'       => 'boolean',
            'is_active'     => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('workshop_date', '>=', now()->toDateString())
                     ->orderBy('workshop_date');
    }
}

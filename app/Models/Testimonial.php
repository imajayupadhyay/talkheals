<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'stars', 'quote', 'name', 'location', 'tag',
        'avatar_gradient', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stars'     => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Derive avatar initial from the first letter of name
    public function getAvatarInitialAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }
}

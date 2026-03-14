<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $fillable = ['section', 'key', 'value'];

    /**
     * Get all key-value pairs for a section, merged with provided defaults.
     */
    public static function getSection(string $section, array $defaults = []): array
    {
        $stored = static::where('section', $section)->pluck('value', 'key')->toArray();

        return array_merge($defaults, $stored);
    }

    /**
     * Upsert all keys for a section from an associative array.
     */
    public static function upsertSection(string $section, array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(
                ['section' => $section, 'key' => $key],
                ['value' => $value]
            );
        }
    }
}

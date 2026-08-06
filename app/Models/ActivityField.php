<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ActivityField extends Model
{
    protected static $specialtyConfigs = [];

    protected $fillable = [
        'name',
        'slug',
        'tag',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_field_user')
            ->withTimestamps();
    }

    /**
     * @return array<string, mixed>
     */
    public function specialtyConfig(): array
    {
        if (! is_string($this->slug) || $this->slug === '') {
            return [];
        }

        if (array_key_exists($this->slug, self::$specialtyConfigs)) {
            return self::$specialtyConfigs[$this->slug];
        }

        $path = config_path("specialties/{$this->slug}.php");

        if (! is_file($path)) {
            return self::$specialtyConfigs[$this->slug] = [];
        }

        $config = require $path;

        return self::$specialtyConfigs[$this->slug] = is_array($config) ? $config : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function specialtySections(): array
    {
        $sections = $this->specialtyConfig()['sections'] ?? [];

        return is_array($sections) ? $sections : [];
    }
}

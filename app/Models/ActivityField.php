<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function userActivityFieldSkills(): HasMany
    {
        return $this->hasMany(UserActivityFieldSkill::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function specialtyQuestions(): array
    {
        $questions = [];

        foreach ($this->specialtySections() as $section) {
            $sectionQuestions = $section['questions'] ?? [];

            if (! is_array($sectionQuestions)) {
                continue;
            }

            foreach ($sectionQuestions as $question) {
                if (is_array($question) && isset($question['key']) && is_string($question['key'])) {
                    $questions[] = $question;
                }
            }
        }

        return $questions;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function specialtyQuestionMap(): array
    {
        $questions = [];

        foreach ($this->specialtyQuestions() as $question) {
            $questions[$question['key']] = $question;
        }

        return $questions;
    }

    /**
     * @return array<int, string>
     */
    public function specialtyQuestionKeys(): array
    {
        return array_keys($this->specialtyQuestionMap());
    }
}

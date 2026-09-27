<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'activity_field_id',
        'name',
        'price',
        'discount_price',
        're_evaluation_price',
        'duration',
        'type',
        'status',
        'description',
        'features',
    ];

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    const TYPE_BASIC = 'basic';

    const TYPE_PRO = 'pro';

    const TYPE_VIP = 'vip';

    const TYPE = [
        self::TYPE_BASIC => 'Basic',
        self::TYPE_PRO => 'Pro',
        self::TYPE_VIP => 'VIP',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activityField(): BelongsTo
    {
        return $this->belongsTo(ActivityField::class);
    }

    /**
     * Normalize features so the API always receives an array of feature objects.
     *
     * @param  mixed  $value
     * @return array<int, array<string, mixed>>
     */
    public function getFeaturesAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $this->normalizeFeaturesArray($value);
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->normalizeFeaturesArray($decoded);
            }

            return collect(preg_split('/\R/u', $value) ?: [])
                ->filter()
                ->map(fn (string $feature) => [
                    'title' => trim($feature),
                    'type' => 'normal',
                ])
                ->values()
                ->all();
        }

        return [];
    }

    /**
     * Store features as JSON for consistent retrieval.
     *
     * @param  mixed  $value
     */
    public function setFeaturesAttribute($value): void
    {
        if (is_string($value)) {
            $this->attributes['features'] = $value;

            return;
        }

        $this->attributes['features'] = json_encode($this->normalizeFeaturesArray((array) $value), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param  array<int, mixed>  $features
     * @return array<int, array<string, mixed>>
     */
    private function normalizeFeaturesArray(array $features): array
    {
        return collect($features)
            ->filter()
            ->map(function ($feature): array {
                if (is_string($feature)) {
                    return [
                        'title' => trim($feature),
                        'type' => 'normal',
                    ];
                }

                if (is_array($feature)) {
                    return [
                        'title' => trim((string) ($feature['title'] ?? $feature['name'] ?? $feature['text'] ?? '')),
                        'type' => in_array(($feature['type'] ?? 'normal'), ['normal', 'special'], true)
                            ? $feature['type']
                            : 'normal',
                    ];
                }

                return [
                    'title' => trim((string) $feature),
                    'type' => 'normal',
                ];
            })
            ->filter(fn (array $feature) => $feature['title'] !== '')
            ->values()
            ->all();
    }
}

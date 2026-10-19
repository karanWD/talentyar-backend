<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends \Spatie\Permission\Models\Role
{
    protected $appends = [
        'activity_field_ids',
    ];

    const PERMISSION_LEVEL_VIEW = 'view';

    const PERMISSION_LEVEL_CONFIRM = 'confirm';

    const PERMISSION_LEVEL_PRICE_EDIT = 'price_edit';

    const PERMISSION_LEVEL_ALL = 'all';

    const PERMISSION_LEVEL = [
        self::PERMISSION_LEVEL_VIEW => 'View',
        self::PERMISSION_LEVEL_CONFIRM => 'Confirm',
        self::PERMISSION_LEVEL_PRICE_EDIT => 'Price Edit',
        self::PERMISSION_LEVEL_ALL => 'All',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'role_category')
            ->withPivot('permission_level')
            ->withTimestamps();
    }

    public function activityFields(): BelongsToMany
    {
        return $this->belongsToMany(ActivityField::class, 'activity_field_role')
            ->withTimestamps();
    }

    /**
     * @return list<int>
     */
    public function getActivityFieldIdsAttribute(): array
    {
        if ($this->relationLoaded('activityFields')) {
            return $this->activityFields->pluck('id')->values()->all();
        }

        return $this->activityFields()
            ->pluck('activity_fields.id')
            ->all();
    }
}

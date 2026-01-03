<?php

namespace App\Models;


class Role extends \Spatie\Permission\Models\Role
{
    const PERMISSION_LEVEL_VIEW = 'view';
    const PERMISSION_LEVEL_CONFIRM = 'confirm';
    const PERMISSION_LEVEL_PRICE_EDIT = 'price_edit';
    const PERMISSION_LEVEL_ALL = 'all';

    const PERMISSION_LEVEL = [
        self::PERMISSION_LEVEL_VIEW => 'View',
        self::PERMISSION_LEVEL_CONFIRM => 'Confirm',
        self::PERMISSION_LEVEL_PRICE_EDIT => 'Price Edit',
        self::PERMISSION_LEVEL_ALL => 'All'
    ];
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'role_category')
            ->withPivot('permission_level')
            ->withTimestamps();
    }

}

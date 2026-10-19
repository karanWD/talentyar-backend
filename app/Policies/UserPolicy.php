<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use App\Services\AdminActivityFieldService;
use App\Services\AdminPermissionService;

class UserPolicy
{
    public function __construct(
        private readonly AdminPermissionService $permissions,
        private readonly AdminActivityFieldService $activityFields,
    ) {}

    public function viewAny(Admin $admin): bool
    {
        return $this->permissions->can($admin, 'read-users');
    }

    public function view(Admin $admin, User $user): bool
    {
        return $this->permissions->can($admin, 'read-users')
            && $this->activityFields->canAccessUser($admin, $user);
    }

    public function create(Admin $admin): bool
    {
        return $this->permissions->can($admin, 'create-users');
    }

    public function update(Admin $admin, User $user): bool
    {
        return $this->permissions->can($admin, 'update-users')
            && $this->activityFields->canAccessUser($admin, $user);
    }

    public function delete(Admin $admin, User $user): bool
    {
        return $this->permissions->can($admin, 'delete-users')
            && $this->activityFields->canManageAllUserData($admin, $user);
    }
}

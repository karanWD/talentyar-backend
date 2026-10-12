<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use App\Services\AdminPermissionService;

class UserPolicy
{
    public function __construct(private readonly AdminPermissionService $permissions) {}

    public function viewAny(Admin $admin): bool
    {
        return $this->permissions->can($admin, 'read-users');
    }

    public function view(Admin $admin, User $user): bool
    {
        return $this->permissions->can($admin, 'read-users');
    }

    public function create(Admin $admin): bool
    {
        return $this->permissions->can($admin, 'create-users');
    }

    public function update(Admin $admin, User $user): bool
    {
        return $this->permissions->can($admin, 'update-users');
    }

    public function delete(Admin $admin, User $user): bool
    {
        return $this->permissions->can($admin, 'delete-users');
    }
}

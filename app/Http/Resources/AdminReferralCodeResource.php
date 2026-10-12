<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Services\AdminFieldPermissionService;
use Illuminate\Http\Request;

class AdminReferralCodeResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        $admin = $request->user('api-admin');
        $fieldPermissions = app(AdminFieldPermissionService::class);
        $canViewReferralCode = $admin instanceof Admin
            && $fieldPermissions->canView($admin, 'users', 'referral_code');
        $canViewPhone = $admin instanceof Admin
            && $fieldPermissions->canView($admin, 'users', 'phone');
        $canViewEmail = $admin instanceof Admin
            && $fieldPermissions->canView($admin, 'users', 'email');

        return [
            'id' => $this->id,
            'referral_code' => $this->when($canViewReferralCode, $this->referral_code),
            'used_count' => (int) ($this->referrals_count ?? 0),
            'owner' => [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
                'username' => $this->username,
                'phone' => $this->when($canViewPhone, $this->phone),
                'email' => $this->when($canViewEmail, $this->email),
            ],
            ...$this->includeTimestamps(),
        ];
    }
}

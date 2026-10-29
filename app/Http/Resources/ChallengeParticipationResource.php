<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Services\AdminFieldPermissionService;
use Illuminate\Http\Request;

class ChallengeParticipationResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        $admin = $request->user('api-admin');
        $fieldPermissions = app(AdminFieldPermissionService::class);
        $canViewPhone = $admin instanceof Admin
            && $fieldPermissions->canView($admin, 'users', 'phone');
        $canViewEmail = $admin instanceof Admin
            && $fieldPermissions->canView($admin, 'users', 'email');
        $user = $this->relationLoaded('user') ? $this->user : null;
        $avatar = $user?->relationLoaded('media')
            ? ($user->media->where('collection', 'avatar')->first() ?? $user->media->first())
            : null;
        $submission = $this->relationLoaded('submission') ? $this->submission : null;

        return [
            'id' => $this->id,
            'challenge_id' => $this->challenge_id,
            'user_id' => $this->user_id,
            'participated_at' => $this->created_at?->toIso8601String(),
            'media' => $submission ? new MediaResource($submission) : null,
            'user' => $user ? [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'full_name' => trim("{$user->first_name} {$user->last_name}") ?: null,
                'username' => $user->username,
                'phone' => $this->when($canViewPhone, $user->phone),
                'email' => $this->when($canViewEmail, $user->email),
                'avatar' => $avatar ? new MinimalMediaResource($avatar) : null,
            ] : null,
        ];
    }
}

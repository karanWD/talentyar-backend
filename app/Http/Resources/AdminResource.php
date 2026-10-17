<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AdminResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'status' => $this->status,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')->values()),
            'role_details' => $this->whenLoaded('roles', fn () => $this->roles
                ->map(fn ($role): array => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'status' => $role->status,
                ])
                ->values()),
            ...$this->includeTimestamps(),
        ];
    }
}

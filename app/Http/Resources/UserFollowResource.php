<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserFollowResource extends BaseResource
{
    /**
     * Get the resource data array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
            'username' => $this->username,
        ];
    }
}


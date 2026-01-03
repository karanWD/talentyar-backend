<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
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
            'phone' => $this->phone,
            'full_name' => $this->full_name,
            'email' => $this->email,
            ...$this->includeTimestamps(),
        ];
    }
}


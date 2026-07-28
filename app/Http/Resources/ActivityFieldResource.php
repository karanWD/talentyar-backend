<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ActivityFieldResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            ...$this->includeTimestamps(),
        ];
    }
}

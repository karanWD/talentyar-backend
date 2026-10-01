<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class NotificationPostResource extends BaseResource
{
    /**
     * Get the resource data array.
     *
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->caption,
            'caption' => $this->caption,
        ];
    }
}

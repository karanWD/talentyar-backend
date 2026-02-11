<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PostResource extends BaseResource
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
            'user_id' => $this->user_id,
            'state' => $this->state,
            'caption' => $this->caption,
            'video' => $this->whenLoaded('media', fn () => $this->media->first() ? new MediaResource($this->media->first()) : null),
            ...$this->includeTimestamps(),
        ];
    }
}

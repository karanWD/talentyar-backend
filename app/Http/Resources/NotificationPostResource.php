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
            'thumbnail' => $this->whenLoaded('media', function () {
                $thumbnail = $this->media->where('collection', 'thumbnail')->first();

                return $thumbnail ? new MinimalMediaResource($thumbnail) : null;
            }),
        ];
    }
}

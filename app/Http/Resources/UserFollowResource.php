<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserFollowResource extends BaseResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
            'username' => $this->username,
            'avatar' => $this->resolveAvatar(),
        ];
    }

    protected function resolveAvatar(): ?MinimalMediaResource
    {
        $items = $this->resource->relationLoaded('media')
            ? $this->resource->getRelation('media')
            : $this->resource->media()->get();
        $media = $items->where('collection', 'avatar')->first() ?? $items->first();

        return $media ? new MinimalMediaResource($media) : null;
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->data['category'],
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'payload' => $this->data['payload'] ?? [],
            'user' => $this->when(
                $this->resource->relationLoaded('actor'),
                fn () => $this->actor ? new UserFollowResource($this->actor) : null
            ),
            'post' => $this->when(
                $this->resource->relationLoaded('post'),
                fn () => $this->post ? new NotificationPostResource($this->post) : null
            ),
            'is_read' => $this->read_at !== null,
            'read_at' => $this->read_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

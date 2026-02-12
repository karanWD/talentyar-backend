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
            'user' => $this->whenLoaded('user', fn () => new UserFollowResource($this->user)),
            'state' => $this->state,
            'caption' => $this->caption,
            'video' => $this->whenLoaded('media', fn () => $this->media->first() ? new MinimalMediaResource($this->media->first()) : null),
            'likes_count' => (int) ($this->likes_count ?? 0),
            'dislikes_count' => (int) ($this->dislikes_count ?? 0),
            'user_has_liked' => $this->when(
                $this->relationLoaded('postLikes'),
                fn () => $this->postLikes->contains('type', \App\Models\PostLike::TYPE_LIKE)
            ),
            'user_has_disliked' => $this->when(
                $this->relationLoaded('postLikes'),
                fn () => $this->postLikes->contains('type', \App\Models\PostLike::TYPE_DISLIKE)
            ),
            ...$this->includeTimestamps(),
        ];
    }
}

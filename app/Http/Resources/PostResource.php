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
            'video' => $this->whenLoaded('media', function () {
                $video = $this->media->where('collection', 'video')->first();
                $fallback = $this->media->first();
                $media = $video ?? $fallback;
                return $media ? new MinimalMediaResource($media) : null;
            }),
            'thumbnail' => $this->whenLoaded('media', function () {
                $thumb = $this->media->where('collection', 'thumbnail')->first();
                return $thumb ? new MinimalMediaResource($thumb) : null;
            }),
            'likes_count' => (int) ($this->likes_count ?? 0),
            'dislikes_count' => (int) ($this->dislikes_count ?? 0),
            'views_count' => (int) ($this->post_views_count ?? 0),
            'user_has_liked' => $this->when(
                $this->relationLoaded('postLikes'),
                fn () => $this->postLikes->contains('type', \App\Models\PostLike::TYPE_LIKE)
            ),
            'user_has_disliked' => $this->when(
                $this->relationLoaded('postLikes'),
                fn () => $this->postLikes->contains('type', \App\Models\PostLike::TYPE_DISLIKE)
            ),
            'comment_count' => $this->comments()->count(),
            ...$this->includeTimestamps(),
        ];
    }
}

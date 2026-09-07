<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;

class PostResource extends BaseResource
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
            'user_id' => $this->user_id,
            'activity_fields_id' => $this->activity_fields_id,
            'user' => $this->whenLoaded('user', fn () => new UserFollowResource($this->user)),
            'type' => $this->type ?? Post::TYPE_VIDEO,
            'state' => $this->state,
            'caption' => $this->caption,
            'height' => $this->height,
            'width' => $this->width,
            'video' => $this->whenLoaded('media', function () {
                $video = $this->media->where('collection', 'video')->first();
                $fallback = $this->type === Post::TYPE_GALLERY ? null : $this->media->first();
                $media = $video ?? $fallback;

                return $media ? new MinimalMediaResource($media) : null;
            }),
            'thumbnail' => $this->whenLoaded('media', function () {
                $thumb = $this->media->where('collection', 'thumbnail')->first();

                return $thumb ? new MinimalMediaResource($thumb) : null;
            }),
            'images' => $this->whenLoaded('media', function () {
                return MinimalMediaResource::collection(
                    $this->media->where('collection', 'gallery')->values()
                );
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
            'comment_count' => (int) ($this->comments_count ?? $this->comments()->count()),
            ...$this->includeTimestamps(),
        ];
    }
}

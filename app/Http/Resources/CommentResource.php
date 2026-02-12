<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CommentResource extends BaseResource
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
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn () => new UserFollowResource($this->user)),
            'body' => $this->body,
            ...$this->includeTimestamps(),
        ];
    }
}

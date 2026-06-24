<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LeaderboardUserResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        $authUser = $request->user();

        return [
            'id' => $this->id,
            'is_me' => $authUser ? $authUser->id === $this->id : false,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
            'username' => $this->username,
            'leaderboard_score' => $this->leaderboard_score !== null ? round((float) $this->leaderboard_score, 2) : null,
            'approved_evaluations_count' => (int) ($this->approved_evaluations_count ?? 0),
            'avatar' => $this->whenLoaded('media', function () {
                $file = $this->media->where('collection', 'avatar')->first();
                $fallback = $this->media->first();
                $media = $file ?? $fallback;

                return $media ? new MinimalMediaResource($media) : null;
            }),
        ];
    }
}

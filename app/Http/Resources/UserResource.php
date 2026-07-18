<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;

class UserResource extends BaseResource
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
            'is_me' => true,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
            'phone' => $this->phone,
            'email' => $this->email,
            'referral_code' => $this->referral_code,
            'referrals_count' => (int) ($this->referrals_count ?? $this->referrals()->count()),
            'referred_by_user_id' => $this->referred_by_user_id,
            'username' => $this->username,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'weight' => $this->weight,
            'height' => $this->height,
            'foot_specialization' => $this->foot_specialization,
            'post_skill' => $this->post_skill,
            'skill_level' => $this->skill_level,
            'activity_history' => $this->activity_history,
            'team_name' => $this->team_name,
            'favorite_iranian_team' => $this->favorite_iranian_team,
            'favorite_foreign_team' => $this->favorite_foreign_team,
            'shirt_number' => $this->shirt_number,
            'bio' => $this->bio,
            'followers_count' => (int) ($this->followers_count ?? $this->followers()->count()),
            'following_count' => (int) ($this->following_count ?? $this->following()->count()),
            'post_count' => (int) ($this->posts()->where('state', Post::STATE_PUBLISHED)->count() ?? $this->posts_count),
            'avatar' => $this->whenLoaded('media', function () {
                $file = $this->media->where('collection', 'avatar')->first();
                $fallback = $this->media->first();
                $media = $file ?? $fallback;
                return $media ? new MinimalMediaResource($media) : null;
            }),
            'subscription' => new SubscriptionResource($this->activeSubscription),
            // Merge in the timestamps, if any
            ...$this->includeTimestamps(),
        ];
    }
}

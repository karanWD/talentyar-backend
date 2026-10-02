<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    /**
     * Get the resource data array.
     *
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        $authenticatedUser = $request->user();
        $is_me = $authenticatedUser instanceof User && $authenticatedUser->id === $this->id;
        $canViewPrivateData = $is_me || $authenticatedUser instanceof Admin;
        $subscriptions = collect();

        if ($canViewPrivateData) {
            $this->resource->loadMissing('subscriptions.plan');
            $subscriptions = $this->subscriptions->sortByDesc('id')->values();
        }

        return [
            'id' => $this->id,
            'is_me' => $is_me,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
            'phone' => $canViewPrivateData ? $this->phone : null,
            'email' => $canViewPrivateData ? $this->email : null,
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
            'post_count' => request()->routeIs('v1.user.get.profile') ? $this->posts()->count() : (int) ($this->posts()->where('state', Post::STATE_PUBLISHED)->count()),
            'avatar' => $this->whenLoaded('media', function () {
                $file = $this->media->where('collection', 'avatar')->first();
                $fallback = $this->media->first();
                $media = $file ?? $fallback;

                return $media ? new MinimalMediaResource($media) : null;
            }),
            'activity_fields' => $canViewPrivateData ? $this->whenLoaded('activityFields', function () use ($request) {
                $skillsByFieldId = $this->relationLoaded('activityFieldSkills')
                    ? $this->activityFieldSkills->keyBy('activity_field_id')
                    : collect();

                return $this->activityFields->map(function ($activityField) use ($request, $skillsByFieldId) {
                    $data = (new ActivityFieldResource($activityField))->toArray($request);
                    $data['skill_answers'] = $skillsByFieldId->get($activityField->id)?->answers;

                    return $data;
                })->values()->all();
            }) : null,
            'subscription' => $canViewPrivateData && $subscriptions->isNotEmpty()
                ? new SubscriptionResource($subscriptions->first())
                : null,
            'subscriptions' => $canViewPrivateData
                ? SubscriptionResource::collection($subscriptions)
                : null,
            // Merge in the timestamps, if any
            ...$this->includeTimestamps(),
        ];
    }
}

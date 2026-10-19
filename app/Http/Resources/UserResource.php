<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\Post;
use App\Models\User;
use App\Services\AdminActivityFieldService;
use App\Services\AdminFieldPermissionService;
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
        $isAdmin = $authenticatedUser instanceof Admin;
        $canViewPrivateData = $is_me || $isAdmin;
        $fieldPermissions = app(AdminFieldPermissionService::class);
        $canViewPhone = ! $isAdmin || $fieldPermissions->canView($authenticatedUser, 'users', 'phone');
        $canViewEmail = ! $isAdmin || $fieldPermissions->canView($authenticatedUser, 'users', 'email');
        $canViewBirthDate = ! $isAdmin || $fieldPermissions->canView($authenticatedUser, 'users', 'birth_date');
        $canViewReferralCode = ! $isAdmin || $fieldPermissions->canView($authenticatedUser, 'users', 'referral_code');
        $canViewSubscription = $is_me
            || ($isAdmin && $fieldPermissions->canView($authenticatedUser, 'users', 'subscription'));
        $subscriptions = collect();
        $allowedActivityFieldIds = [];

        if ($canViewSubscription) {
            if ($isAdmin) {
                $activityFields = app(AdminActivityFieldService::class);
                $allowedActivityFieldIds = $activityFields->ids($authenticatedUser);
                $this->resource->loadMissing([
                    'subscriptions' => fn ($query) => $activityFields
                        ->scopeSubscriptions($query, $authenticatedUser)
                        ->with('plan'),
                ]);
            } else {
                $this->resource->loadMissing('subscriptions.plan');
            }

            $subscriptions = $this->subscriptions
                ->when(
                    $isAdmin,
                    fn ($items) => $items->whereIn('activity_field_id', $allowedActivityFieldIds)
                )
                ->sortByDesc('id')
                ->values();
        }

        $postCountQuery = $this->posts()
            ->getQuery()
            ->where('state', Post::STATE_PUBLISHED);

        if ($isAdmin) {
            $postCountQuery = app(AdminActivityFieldService::class)
                ->scopePosts($postCountQuery, $authenticatedUser);
        }

        return [
            'id' => $this->id,
            'is_me' => $is_me,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
            'phone' => $isAdmin
                ? $this->when($canViewPhone, $this->phone)
                : ($canViewPrivateData ? $this->phone : null),
            'email' => $isAdmin
                ? $this->when($canViewEmail, $this->email)
                : ($canViewPrivateData ? $this->email : null),
            'referral_code' => $isAdmin
                ? $this->when($canViewReferralCode, $this->referral_code)
                : $this->referral_code,
            'referrals_count' => (int) ($this->referrals_count ?? $this->referrals()->count()),
            'referred_by_user_id' => $this->referred_by_user_id,
            'username' => $this->username,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'gender' => $this->gender,
            'birth_date' => $isAdmin
                ? $this->when($canViewBirthDate, $this->birth_date)
                : $this->birth_date,
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
            'post_count' => request()->routeIs('v1.user.get.profile')
                ? $this->posts()->count()
                : $postCountQuery->count(),
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
            'subscription' => $isAdmin
                ? $this->when(
                    $canViewSubscription,
                    $subscriptions->isNotEmpty() ? new SubscriptionResource($subscriptions->first()) : null
                )
                : ($canViewSubscription && $subscriptions->isNotEmpty()
                    ? new SubscriptionResource($subscriptions->first())
                    : null),
            'subscriptions' => $isAdmin
                ? $this->when($canViewSubscription, SubscriptionResource::collection($subscriptions))
                : ($canViewSubscription ? SubscriptionResource::collection($subscriptions) : null),
            // Merge in the timestamps, if any
            ...$this->includeTimestamps(),
        ];
    }
}

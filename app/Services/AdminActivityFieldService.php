<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Evaluation;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdminActivityFieldService
{
    /** @var array<int, list<int>> */
    private array $activityFieldIds = [];

    public function __construct(private readonly AdminPermissionRegistry $registry) {}

    /**
     * Return the union of activity fields assigned to the admin's active roles.
     *
     * @return list<int>
     */
    public function ids(Admin $admin): array
    {
        if (isset($this->activityFieldIds[$admin->id])) {
            return $this->activityFieldIds[$admin->id];
        }

        $admin->loadMissing('roles.activityFields:id');

        return $this->activityFieldIds[$admin->id] = $admin->roles
            ->where('guard_name', $this->registry->guard())
            ->where('status', 'active')
            ->flatMap->activityFields
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function canAccessActivityField(Admin $admin, ?int $activityFieldId): bool
    {
        return $activityFieldId !== null
            && in_array($activityFieldId, $this->ids($admin), true);
    }

    public function canAccessUser(Admin $admin, User $user): bool
    {
        return $this->scopeUsers(User::query()->whereKey($user->getKey()), $admin)->exists();
    }

    public function canManageAllUserData(Admin $admin, User $user): bool
    {
        if (! $this->canAccessUser($admin, $user)) {
            return false;
        }

        $ids = $this->ids($admin);

        return ! $user->posts()
            ->where(fn (Builder $query) => $query
                ->whereNull('activity_fields_id')
                ->orWhereNotIn('activity_fields_id', $ids))
            ->exists()
            && ! $user->evaluations()
                ->where(fn (Builder $query) => $query
                    ->whereNull('activity_fields_id')
                    ->orWhereNotIn('activity_fields_id', $ids))
                ->exists()
            && ! $user->subscriptions()
                ->where(fn (Builder $query) => $query
                    ->whereNull('activity_field_id')
                    ->orWhereNotIn('activity_field_id', $ids))
                ->exists();
    }

    public function canAccessPost(Admin $admin, Post $post): bool
    {
        $post->loadMissing('user');

        return $this->canAccessActivityField($admin, $post->activity_fields_id)
            && $post->user instanceof User
            && $this->canAccessUser($admin, $post->user);
    }

    public function canAccessEvaluation(Admin $admin, Evaluation $evaluation): bool
    {
        $evaluation->loadMissing('user');

        return $this->canAccessActivityField($admin, $evaluation->activity_fields_id)
            && $evaluation->user instanceof User
            && $this->canAccessUser($admin, $evaluation->user)
            && $evaluation->posts()->count() === $this->scopePosts(
                $evaluation->posts()->getQuery(),
                $admin
            )->count();
    }

    public function scopeUsers(Builder $query, Admin $admin): Builder
    {
        $ids = $this->ids($admin);

        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereHas('activityFields')
            ->whereDoesntHave(
                'activityFields',
                fn (Builder $activityFields) => $activityFields->whereNotIn('activity_fields.id', $ids)
            );
    }

    public function scopePosts(Builder $query, Admin $admin): Builder
    {
        $ids = $this->ids($admin);

        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereIn('posts.activity_fields_id', $ids)
            ->whereHas(
                'user',
                fn (Builder $users) => $this->scopeUsers($users, $admin)
            );
    }

    public function scopeEvaluations(Builder $query, Admin $admin): Builder
    {
        $ids = $this->ids($admin);

        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereIn('evaluations.activity_fields_id', $ids)
            ->whereHas(
                'user',
                fn (Builder $users) => $this->scopeUsers($users, $admin)
            );
    }

    public function scopeSubscriptions(Builder $query, Admin $admin): Builder
    {
        $ids = $this->ids($admin);

        if ($ids === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereIn('subscriptions.activity_field_id', $ids)
            ->whereHas(
                'user',
                fn (Builder $users) => $this->scopeUsers($users, $admin)
            );
    }

    public function scopeItems(Builder $query, Admin $admin): Builder
    {
        $ids = $this->ids($admin);

        return $ids === []
            ? $query->whereRaw('1 = 0')
            : $query->whereIn('items.activity_field_id', $ids);
    }
}

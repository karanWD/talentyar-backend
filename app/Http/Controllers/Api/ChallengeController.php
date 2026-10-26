<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ChallengeParticipationResource;
use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengeController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'activity_field_id' => ['nullable', 'integer', 'exists:activity_fields,id'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $query = Challenge::query()
            ->currentlyActive()
            ->with(['activityField', 'trainingVideo'])
            ->withCount('participations')
            ->withExists([
                'participations as has_joined' => fn ($participations) => $participations
                    ->where('user_id', $user->id),
            ]);

        if (isset($filters['activity_field_id'])) {
            $query->where('activity_field_id', $filters['activity_field_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($nested) use ($search): void {
                $nested->where('title', 'like', '%'.$search.'%')
                    ->orWhere('prize', 'like', '%'.$search.'%');
            });
        }

        $challenges = $query
            ->latest('activated_at')
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();

        $hasActiveSubscription = $user->activeSubscriptions()->exists();
        $challenges->getCollection()->each(
            fn (Challenge $challenge) => $challenge->setAttribute(
                'can_join',
                $hasActiveSubscription && ! $challenge->has_joined
            )
        );

        return $this->successResponse(
            array_merge(
                ['challenges' => ChallengeResource::collection($challenges)->resolve()],
                $this->paginationMeta($challenges)
            ),
            'Challenges retrieved successfully'
        );
    }

    public function show(Request $request, Challenge $challenge): JsonResponse
    {
        if (! $challenge->isCurrentlyActive()) {
            return $this->errorResponse('Challenge not found', 404);
        }

        /** @var User $user */
        $user = $request->user();
        $challenge->load(['activityField', 'trainingVideo'])
            ->loadCount('participations')
            ->loadExists([
                'participations as has_joined' => fn ($participations) => $participations
                    ->where('user_id', $user->id),
            ]);
        $challenge->setAttribute(
            'can_join',
            $user->activeSubscriptions()->exists() && ! $challenge->has_joined
        );

        return $this->successResponse(
            ['challenge' => new ChallengeResource($challenge)],
            'Challenge retrieved successfully'
        );
    }

    public function participate(Request $request, Challenge $challenge): JsonResponse
    {
        if (! $challenge->isCurrentlyActive()) {
            return $this->errorResponse('Challenge is not active', 422);
        }

        /** @var User $user */
        $user = $request->user();

        if (! $user->activeSubscriptions()->exists()) {
            return $this->errorResponse('An active subscription is required to participate', 403);
        }

        if ($challenge->participations()->where('user_id', $user->id)->exists()) {
            return $this->errorResponse('User has already participated in this challenge', 409);
        }

        try {
            $participation = $challenge->participations()->create([
                'user_id' => $user->id,
            ]);
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintViolation($exception)) {
                return $this->errorResponse('User has already participated in this challenge', 409);
            }

            throw $exception;
        }

        $participation->load('user.media');

        return $this->successResponse(
            ['participation' => new ChallengeParticipationResource($participation)],
            'Challenge participation registered successfully',
            201
        );
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return in_array($exception->getCode(), ['23000', '23505'], true)
            || str_contains(strtolower($exception->getMessage()), 'unique constraint');
    }
}

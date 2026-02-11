<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends BaseApiController
{
    /**
     * Get the authenticated user's followers (paginated).
     */
    public function followers(Request $request): JsonResponse
    {
        $users = $request->user()
            ->followers()
            ->latest('user_follows.created_at')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            ['followers' => UserResource::collection($users)],
            'Followers retrieved successfully'
        );
    }

    /**
     * Get the authenticated user's following list (paginated).
     */
    public function following(Request $request): JsonResponse
    {
        $users = $request->user()
            ->following()
            ->latest('user_follows.created_at')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            ['following' => UserResource::collection($users)],
            'Following retrieved successfully'
        );
    }

    /**
     * Follow a user.
     */
    public function follow(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        if ($currentUser->id === $user->id) {
            return $this->errorResponse('You cannot follow yourself.', 422);
        }

        if ($currentUser->following()->where('following_id', $user->id)->exists()) {
            return $this->successResponse(
                ['user' => new UserResource($user)],
                'Already following this user'
            );
        }

        $currentUser->following()->attach($user->id);

        return $this->successResponse(
            ['user' => new UserResource($user)],
            'Successfully followed user',
            201
        );
    }

    /**
     * Unfollow a user.
     */
    public function unfollow(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        $currentUser->following()->detach($user->id);

        return $this->successResponse(
            ['user' => new UserResource($user)],
            'Successfully unfollowed user'
        );
    }
}

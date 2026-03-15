<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    /**
     * Search users by username (partial match). Returns matching users with counts only.
     */
    public function search(Request $request): JsonResponse
    {
        $username = $request->query('username');
        if (empty($username) || !is_string($username)) {
            return $this->errorResponse('Username parameter is required', 422);
        }

        $users = User::query()
            ->where('username', 'like', '%' . $username . '%')
            ->withCount(['followers', 'following', 'posts'])
            ->orderByRaw("CASE WHEN username = ? THEN 0 ELSE 1 END", [$username])
            ->orderBy('username')
            ->limit(20)
            ->get();

        $data = $users->map(fn (User $user) => [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => trim("{$user->first_name} {$user->last_name}") ?: null,
            'username' => $user->username,
            'followers_count' => (int) $user->followers_count,
            'following_count' => (int) $user->following_count,
            'is_following' => auth()->check() ? $user->followers()->where('follower_id', auth()->id())->exists() : false,
            'posts_count' => (int) $user->posts_count,
        ]);

        return $this->successResponse(
            ['users' => $data],
            'Users retrieved successfully'
        );
    }

    /**
     * Get a user profile by exact username with posts and counts.
     */
    public function showByUsername(Request $request, string $username): JsonResponse
    {
        $user = User::query()
            ->where('username', $username)
            ->withCount(['followers', 'following', 'posts'])
            ->first();

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $authUser = $request->user();
        $perPage = $request->integer('per_page', 15);
        $posts = $user->posts()
            ->where('state', Post::STATE_PUBLISHED)
            ->withCount(['likes', 'dislikes', 'postViews'])
            ->with([
                'media',
                'user',
                ...($authUser ? ['postLikes' => fn ($q) => $q->where('user_id', $authUser->id)] : []),
            ])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return $this->successResponse([
            'user' => new UserResource($user),
            'posts' => PostResource::collection($posts),
            'followers_count' => (int) $user->followers_count,
            'following_count' => (int) $user->following_count,
            'is_following' => auth()->check() ? $user->followers()->where('follower_id', auth()->id())->exists() : false,
            'posts_count' => (int) $user->posts_count,
        ], 'Profile retrieved successfully');
    }
}

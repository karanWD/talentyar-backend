<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    private function authorizeOrFail(Request $request, string $permission): ?JsonResponse
    {
        $admin = $request->user('api-admin');
        if (! $admin || ! $admin->can($permission)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-users')) {
            return $error;
        }


        $users = User::query()
            ->latest()
            ->paginate();

        return $this->successResponse(
            array_merge(
                ['users' => UserResource::collection($users)->resolve()],
                $this->paginationMeta($users)
            ),
            'Users retrieved successfully'
        );
    }



    public function show(Request $request, User $user): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-users')) {
            return $error;
        }

        $user->load('media')->withCount(['followers', 'following', 'posts']);

        $perPage = $request->integer('per_page', 15);
        $posts = $user->posts()
            ->withCount(['likes', 'dislikes', 'postViews'])
            ->with(['media'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                [
                    'user' => (new UserResource($user))->resolve(),
                    'posts' => PostResource::collection($posts)->resolve(),
                ],
                $this->paginationMeta($posts)
            ),
            'User Details retrieved successfully'
        );

    }


}

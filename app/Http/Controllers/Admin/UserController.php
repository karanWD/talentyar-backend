<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Evaluation;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->when($request->has('subscription_type'),function ($q) use ($request){
                if($request->get('subscription_type') == 'no') {
                    // where does not have active subscription
                    $q->doesntHave('activeSubscription');
                }else {
                    $q->whereHas('activeSubscription',function ($q) use ($request){
                        $q->whereHas('plan',function ($q) use ($request){
                            $q->where('type',$request->get('subscription_type'));
                        });
                    });
                }
            })
            ->paginate($request->integer('per_page', 15));

        return $this->successResponse(
            array_merge(
                ['users' => UserResource::collection($users)->resolve()],
                $this->paginationMeta($users)
            ),
            'Users retrieved successfully'
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'create-users')) {
            return $error;
        }

        $user = User::create($request->validated());
        $user->loadCount(['followers', 'following', 'posts']);

        return $this->successResponse(
            ['user' => (new UserResource($user))->resolve()],
            'User created successfully',
            201
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-users')) {
            return $error;
        }

        $user->update($request->validated());
        $user->loadCount(['followers', 'following', 'posts']);

        return $this->successResponse(
            ['user' => (new UserResource($user))->resolve()],
            'User updated successfully'
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

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'delete-users')) {
            return $error;
        }

        DB::transaction(function () use ($user): void {
            $this->deleteUserRelations($user);
            $user->delete();
        });

        return $this->successResponse(
            null,
            'User deleted successfully'
        );
    }

    private function deleteUserRelations(User $user): void
    {
        $user->tokens()->delete();
        $user->media()->delete();

        $user->posts()
            ->with('media')
            ->chunkById(100, function ($posts): void {
                /** @var Post $post */
                foreach ($posts as $post) {
                    $post->media()->delete();
                    $post->delete();
                }
            });

        $user->evaluations()
            ->with(['items', 'media'])
            ->chunkById(100, function ($evaluations): void {
                /** @var Evaluation $evaluation */
                foreach ($evaluations as $evaluation) {
                    $evaluation->items()->delete();
                    $evaluation->media()->delete();
                    $evaluation->delete();
                }
            });
    }


}

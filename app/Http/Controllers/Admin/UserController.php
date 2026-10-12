<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\AdminReferralCodeResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\Evaluation;
use App\Models\Post;
use App\Models\User;
use App\Services\AdminFieldPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UserController extends BaseApiController
{
    public function __construct(private readonly AdminFieldPermissionService $fieldPermissions) {}

    private function authorizeOrFail(Request $request, string $ability, User|string $subject): ?JsonResponse
    {
        $admin = $request->user('api-admin');
        if (! $admin instanceof Admin || ! Gate::forUser($admin)->allows($ability, $subject)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'viewAny', User::class)) {
            return $error;
        }

        /** @var Admin $admin */
        $admin = $request->user('api-admin');
        $canViewSubscription = $this->fieldPermissions->canView($admin, 'users', 'subscription');

        if ($request->has('subscription_type') && ! $canViewSubscription) {
            return $this->errorResponse(
                'You are not allowed to filter by subscription.',
                403,
                ['forbidden_fields' => ['subscription']]
            );
        }

        $users = User::query()
            ->when($canViewSubscription, fn ($query) => $query->with('subscriptions.plan'))
            ->latest()
            ->when($request->has('subscription_type'), function ($q) use ($request) {
                if ($request->get('subscription_type') == 'no') {
                    // where does not have active subscription
                    $q->doesntHave('activeSubscription');
                } else {
                    $q->whereHas('activeSubscription', function ($q) use ($request) {
                        $q->whereHas('plan', function ($q) use ($request) {
                            $q->where('type', $request->get('subscription_type'));
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

    public function referralCodes(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'viewAny', User::class)) {
            return $error;
        }

        /** @var Admin $admin */
        $admin = $request->user('api-admin');

        if (! $this->fieldPermissions->canView($admin, 'users', 'referral_code')) {
            return $this->errorResponse(
                'You are not allowed to view referral codes.',
                403,
                ['forbidden_fields' => ['referral_code']]
            );
        }

        $canViewPhone = $this->fieldPermissions->canView($admin, 'users', 'phone');

        $referralCodes = User::query()
            ->whereNotNull('referral_code')
            ->withCount('referrals')
            ->when($request->filled('search'), function ($query) use ($request, $canViewPhone): void {
                $search = $request->string('search')->toString();

                $query->where(function ($nested) use ($search, $canViewPhone): void {
                    $nested->where('referral_code', 'like', '%'.$search.'%')
                        ->orWhere('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%');

                    if ($canViewPhone) {
                        $nested->orWhere('phone', 'like', '%'.$search.'%');
                    }
                });
            })
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                ['referral_codes' => AdminReferralCodeResource::collection($referralCodes)->resolve()],
                $this->paginationMeta($referralCodes)
            ),
            'Referral codes retrieved successfully'
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'create', User::class)) {
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
        if ($error = $this->authorizeOrFail($request, 'update', $user)) {
            return $error;
        }

        /** @var Admin $admin */
        $admin = $request->user('api-admin');
        $validated = $request->validated();
        $forbiddenFields = $this->fieldPermissions->forbiddenFields(
            $admin,
            'users',
            array_keys($validated),
            'update'
        );

        if ($forbiddenFields !== []) {
            return $this->errorResponse(
                'You are not allowed to update some fields.',
                403,
                ['forbidden_fields' => $forbiddenFields]
            );
        }

        $user->update($validated);
        $user->loadCount(['followers', 'following', 'posts']);

        return $this->successResponse(
            ['user' => (new UserResource($user))->resolve()],
            'User updated successfully'
        );
    }

    public function show(Request $request, User $user): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'view', $user)) {
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
        if ($error = $this->authorizeOrFail($request, 'delete', $user)) {
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Admin;
use App\Models\Item;
use App\Services\AdminActivityFieldService;
use App\Services\AdminPermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends BaseApiController
{
    public function __construct(
        private readonly AdminPermissionService $permissions,
        private readonly AdminActivityFieldService $activityFields,
    ) {}

    private function authorizeOrFail(Request $request, string $permission): ?JsonResponse
    {
        $admin = $request->user('api-admin');
        if (! $admin instanceof Admin || ! $this->permissions->can($admin, $permission)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-items')) {
            return $error;
        }

        /** @var Admin $admin */
        $admin = $request->user('api-admin');
        $query = $this->activityFields
            ->scopeItems(Item::query(), $admin)
            ->orderBy('id');

        if ($request->filled('activity_field_id')) {
            $query->where('activity_field_id', $request->integer('activity_field_id'));
        }

        return $this->successResponse(
            ['items' => $query->get(['id', 'name', 'activity_field_id'])],
            'Items retrieved successfully'
        );
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends BaseApiController
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
        $query = Item::query()->orderBy('id');

        if ($request->filled('activity_field_id')) {
            $query->where('activity_field_id', $request->integer('activity_field_id'));
        }

        return $this->successResponse(
            ['items' => $query->get(['id', 'name', 'activity_field_id'])],
            'Items retrieved successfully'
        );
    }
}

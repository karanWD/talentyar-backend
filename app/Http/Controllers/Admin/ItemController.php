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
        if ($error = $this->authorizeOrFail($request, 'update-evaluation')) {
            return $error;
        }

        $query = Item::query()->orderBy('id');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        return $this->successResponse(
            ['items' => $query->get(['id', 'name', 'type'])],
            'Items retrieved successfully'
        );
    }
}

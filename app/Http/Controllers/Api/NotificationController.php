<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $user = $request->user();

        $user->unreadNotifications()->update(['read_at' => now()]);

        $notifications = $user->notifications()
            ->latest()
            ->paginate($validated['per_page'] ?? 15)
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                ['notifications' => NotificationResource::collection($notifications)->resolve()],
                $this->paginationMeta($notifications)
            ),
            'Notifications retrieved successfully'
        );
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $updated = $request->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return $this->successResponse(
            ['updated_count' => $updated],
            'Notifications marked as read successfully'
        );
    }
}

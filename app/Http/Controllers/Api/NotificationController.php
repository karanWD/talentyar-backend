<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use App\Models\Post;
use App\Models\User;
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

        $notifications = $user->notifications()
            ->latest()
            ->paginate($validated['per_page'] ?? 15)
            ->withQueryString();

        $notificationItems = $notifications->getCollection();
        $actorIds = $notificationItems
            ->pluck('data.payload.user_id')
            ->filter()
            ->unique()
            ->values();
        $postIds = $notificationItems
            ->pluck('data.payload.post_id')
            ->filter()
            ->unique()
            ->values();

        $actors = User::query()
            ->with('media')
            ->whereKey($actorIds)
            ->get()
            ->keyBy('id');
        $posts = Post::query()
            ->whereKey($postIds)
            ->get()
            ->keyBy('id');

        $notificationItems->each(function ($notification) use ($actors, $posts): void {
            $actorId = data_get($notification->data, 'payload.user_id');
            $postId = data_get($notification->data, 'payload.post_id');

            if ($actorId !== null) {
                $notification->setRelation('actor', $actors->get($actorId));
            }

            if ($postId !== null) {
                $notification->setRelation('post', $posts->get($postId));
            }
        });

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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\PostResource;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PostController extends BaseApiController
{
    private function authorizeOrFail(Request $request, string $permission): ?JsonResponse
    {
        $admin = $request->user('api-admin');
        if (! $admin || ! $admin->can($permission)) {
            return $this->errorResponse('Forbidden', 403);
        }

        return null;
    }

    /**
     * Paginated list of posts (optional filters: state, user_id, search).
     */
    public function index(Request $request): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-posts')) {
            return $error;
        }

        $query = Post::query()
            ->with(['user', 'media'])
            ->withCount(['likes', 'dislikes', 'postViews', 'comments']);

        if ($request->filled('state')) {
            $query->where('state', $request->integer('state'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('caption', 'like', '%'.$search.'%');
        }

        $posts = $query
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            array_merge(
                ['posts' => PostResource::collection($posts)->resolve()],
                $this->paginationMeta($posts)
            ),
            'Posts retrieved successfully'
        );
    }

    /**
     * Single post detail.
     */
    public function show(Request $request, Post $post): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'read-posts')) {
            return $error;
        }

        $post->load(['user', 'media']);
        $post->loadCount(['likes', 'dislikes', 'postViews', 'comments']);

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Post retrieved successfully'
        );
    }

    /**
     * Update post moderation state (published, draft, rejected).
     */
    public function updateState(Request $request, Post $post): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'update-posts')) {
            return $error;
        }

        $validated = $request->validate([
            'state' => ['required', 'integer', 'in:'.implode(',', Post::STATES)],
        ]);

        $post->update(['state' => $validated['state']]);
        $post->load(['user', 'media']);
        $post->loadCount(['likes', 'dislikes', 'postViews', 'comments']);

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Post state updated successfully'
        );
    }

    /**
     * Delete a post and all of its attached files.
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        if ($error = $this->authorizeOrFail($request, 'delete-posts')) {
            return $error;
        }

        $post->load('media');

        DB::transaction(function () use ($post): void {
            /** @var Media $media */
            foreach ($post->media as $media) {
                $isUsedElsewhere = Media::query()
                    ->whereKeyNot($media->getKey())
                    ->where('disk', $media->disk)
                    ->where('path', $media->path)
                    ->exists();

                if (! $isUsedElsewhere) {
                    $disk = $media->disk ?: 's3';

                    if (! Storage::disk($disk)->delete($media->path)) {
                        throw new RuntimeException("Unable to delete post media from disk [{$disk}].");
                    }
                }

                $media->forceDelete();
            }

            $post->delete();
        });

        return $this->successResponse(
            null,
            'Post deleted successfully'
        );
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends BaseApiController
{
    /**
     * Get current user's posts (paginated).
     */
    public function myPosts(Request $request): JsonResponse
    {
        $posts = $request->user()
            ->posts()
            ->with(['media', 'user'])
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            ['posts' => PostResource::collection($posts)],
            'Posts retrieved successfully'
        );
    }

    /**
     * Get feed: published posts from users the current user follows (paginated).
     */
    public function feed(Request $request): JsonResponse
    {
        $followingIds = Auth::user()->following()->pluck('user_follows.id');

        $posts = Post::query()
            ->where('state', Post::STATE_PUBLISHED)
            ->whereIn('user_id', $followingIds)
            ->with(['media', 'user'])
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            ['posts' => PostResource::collection($posts)],
            'Feed retrieved successfully'
        );
    }

    /**
     * Create a post with one video (attach media by hash).
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $user = $request->user();

        $post = Post::create([
            'user_id' => $user->id,
            'caption' => $request->validated('caption'),
            'state' => $request->validated('state', Post::STATE_PUBLISHED),
        ]);

        $media = Media::where('hash', $request->validated('video_hash'))->first();
        $post->assignMedia($media);

        $post->load('media');

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Post created successfully',
            201
        );
    }
}

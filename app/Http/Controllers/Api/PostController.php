<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Media;
use App\Models\Post;
use App\Models\PostLike;
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
        $user = $request->user();
        $posts = $user->posts()
            ->withCount(['likes', 'dislikes'])
            ->with(['media', 'user', 'postLikes' => fn ($q) => $q->where('user_id', $user->id)])
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
            ->withCount(['likes', 'dislikes'])
            ->with(['media', 'user', 'postLikes' => fn ($q) => $q->where('user_id', Auth::user()->id)])
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
        $post->loadCount(['likes', 'dislikes']);

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Post created successfully',
            201
        );
    }

    /**
     * Like a post (idempotent: already liked does nothing).
     */
    public function like(Request $request, Post $post): JsonResponse
    {
        PostLike::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => $request->user()->id],
            ['type' => PostLike::TYPE_LIKE]
        );

        $post->loadCount(['likes', 'dislikes']);
        $post->load(['postLikes' => fn ($q) => $q->where('user_id', $request->user()->id)]);

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Post liked'
        );
    }

    /**
     * Dislike a post (idempotent: already disliked does nothing).
     */
    public function dislike(Request $request, Post $post): JsonResponse
    {
        PostLike::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => $request->user()->id],
            ['type' => PostLike::TYPE_DISLIKE]
        );

        $post->loadCount(['likes', 'dislikes']);
        $post->load(['postLikes' => fn ($q) => $q->where('user_id', $request->user()->id)]);

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Post disliked'
        );
    }

    /**
     * Remove like/dislike from a post.
     */
    public function removeReaction(Request $request, Post $post): JsonResponse
    {
        $post->postLikes()->where('user_id', $request->user()->id)->delete();

        $post->loadCount(['likes', 'dislikes']);
        $post->setRelation('postLikes', collect());

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Reaction removed'
        );
    }
}

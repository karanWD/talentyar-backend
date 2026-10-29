<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use App\Notifications\InternalNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PostController extends BaseApiController
{
    /**
     * Get current user's posts (paginated).
     */
    public function myPosts(Request $request): JsonResponse
    {
        $user = $request->user();
        $posts = $user->posts()
            ->withCount(['likes', 'dislikes', 'postViews'])
            ->with(['media', 'user', 'postLikes' => fn ($q) => $q->where('user_id', $user->id)])
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            ['posts' => PostResource::collection($posts)],
            'Posts retrieved successfully'
        );
    }

    public function userPosts(Request $request, $username): JsonResponse
    {
        $user = User::query()
            ->with('media')
            ->where('username', $username)
            ->withCount(['followers', 'following', 'posts'])
            ->first();

        if (! $user) {
            return $this->errorResponse('User not found', 404);
        }

        $posts = $user->posts()
            ->where('state', Post::STATE_PUBLISHED)
            ->withCount(['likes', 'dislikes', 'postViews'])
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
        $followingIds = Auth::user()->following()->pluck('user_follows.following_id');

        $posts = Post::query()
            ->where('state', Post::STATE_PUBLISHED)
            ->whereIn('user_id', $followingIds)
            ->withCount(['likes', 'dislikes', 'postViews'])
            ->with(['media', 'user', 'postLikes' => fn ($q) => $q->where('user_id', Auth::user()->id)])
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        //        if($posts->count() == 0){
        //            $posts = Post::query()
        //                ->where('state', Post::STATE_PUBLISHED)
        //                ->with(['media', 'user', 'postLikes' => fn ($q) => $q->where('user_id', Auth::user()->id)])
        //                ->inRandomOrder()
        //                ->paginate($request->integer('per_page', 15))
        //                ->withQueryString();
        //        }

        return $this->successResponse(
            ['posts' => PostResource::collection($posts)],
            'Feed retrieved successfully'
        );
    }

    public function explore(Request $request): JsonResponse
    {
        // Show posts that have more views, in random order

        $posts = Post::query()
            ->where('state', Post::STATE_PUBLISHED)
            ->withCount(['likes', 'dislikes', 'postViews'])
            ->with(['media', 'user', 'postLikes' => fn ($q) => $q->where('user_id', Auth::user()->id)])
            ->orderByDesc('created_at')
//            ->orderByDesc('post_views_count')
//            ->inRandomOrder()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            ['posts' => PostResource::collection($posts)],
            'Feed retrieved successfully'
        );
    }

    /**
     * Create a video or gallery post (attach media by hash).
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->validated('type', Post::TYPE_VIDEO);

        $post = Post::create([
            'user_id' => $user->id,
            'activity_fields_id' => $request->validated('activity_fields_id'),
            'type' => $type,
            'caption' => $request->validated('caption'),
            'height' => $request->validated('height'),
            'width' => $request->validated('width'),
            'state' => $request->validated('state', Post::STATE_DRAFT),
        ]);

        if ($type === Post::TYPE_VIDEO) {
            $videoMedia = Media::where('hash', $request->validated('video_hash'))->firstOrFail();
            $post->assignMedia($videoMedia, 'video');

            if ($request->filled('thumbnail_hash')) {
                $thumbnailMedia = Media::where('hash', $request->validated('thumbnail_hash'))->firstOrFail();
                $post->assignMedia($thumbnailMedia, 'thumbnail');
            }
        } else {
            $imageMedia = Media::whereIn('hash', $request->validated('image_hashes'))
                ->get()
                ->keyBy('hash');

            if ($request->filled('thumbnail_hash')) {
                $thumbnailMedia = Media::where('hash', $request->validated('thumbnail_hash'))->firstOrFail();
                $post->assignMedia($thumbnailMedia, 'thumbnail');
            }

            foreach ($request->validated('image_hashes') as $imageHash) {
                $post->assignMedia($imageMedia->get($imageHash), 'gallery');
            }
        }

        $post->load('media');
        $post->loadCount(['likes', 'dislikes', 'postViews']);

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Post created successfully',
            201
        );
    }

    /**
     * Delete one of the current user's posts and all of its attached files.
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        if ($post->user_id !== $request->user()->id) {
            return $this->errorResponse('Forbidden', 403);
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

    /**
     * Like a post (idempotent: already liked does nothing).
     */
    public function like(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        $previousReaction = PostLike::query()
            ->where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->value('type');

        PostLike::updateOrCreate(
            ['post_id' => $post->id, 'user_id' => $user->id],
            ['type' => PostLike::TYPE_LIKE]
        );

        if ($post->user_id !== $user->id && $previousReaction !== PostLike::TYPE_LIKE) {
            $post->user->notify(InternalNotification::postLiked($post, $user));
        }

        $post->loadCount(['likes', 'dislikes', 'postViews']);
        $post->load(['postLikes' => fn ($q) => $q->where('user_id', $user->id)]);

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

        $post->loadCount(['likes', 'dislikes', 'postViews']);
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

        $post->loadCount(['likes', 'dislikes', 'postViews']);
        $post->setRelation('postLikes', collect());

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'Reaction removed'
        );
    }

    /**
     * List comments for a post (paginated).
     */
    public function comments(Request $request, Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return $this->successResponse(
            ['comments' => CommentResource::collection($comments)],
            'Comments retrieved successfully'
        );
    }

    /**
     * Record a view for a post (logged-in user). Each call counts as one view.
     */
    public function recordView(Request $request, Post $post): JsonResponse
    {
        $post->postViews()->create([
            'user_id' => $request->user()->id,
        ]);

        $post->loadCount(['likes', 'dislikes', 'postViews']);
        $post->load(['media', 'user', 'postLikes' => fn ($q) => $q->where('user_id', $request->user()->id)]);

        return $this->successResponse(
            ['post' => new PostResource($post)],
            'View recorded'
        );
    }

    /**
     * Add a comment to a post.
     */
    public function storeComment(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $user = $request->user();
        $comment = $post->comments()->create([
            'user_id' => $user->id,
            'body' => $request->validated('body'),
        ]);

        if ($post->user_id !== $user->id) {
            $post->user->notify(InternalNotification::postCommented($post, $comment, $user));
        }

        $comment->load('user');

        return $this->successResponse(
            ['comment' => new CommentResource($comment)],
            'Comment added',
            201
        );
    }
}

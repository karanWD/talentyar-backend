<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends BaseApiController
{
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

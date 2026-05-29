<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UploadMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;

class MediaController extends BaseApiController
{
    /**
     * Upload a single photo or video. Returns media with hash to attach to user or post.
     */
    public function upload(UploadMediaRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $mediaService = new MediaService();

        $media = $mediaService->addMediaFromUploadedFile(
            $file,
            'user',
            $request->validated('type', Media::TYPE_FILE),
            'default',
            's3',
            $request->only(['alt'])
        );

        return $this->successResponse(
            [
                'media' => new MediaResource($media),
                'hash' => $media->hash,
            ],
            'Media uploaded successfully',
            201
        );
    }
}

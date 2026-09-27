<?php

namespace App\Http\Requests;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activity_fields_id' => ['required', 'integer', 'exists:activity_fields,id'],
            'caption' => ['required', 'string', 'max:5000'],
            'height' => ['required', 'integer', 'min:1'],
            'width' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'string', 'in:'.implode(',', Post::TYPES)],
            'video_hash' => [
                'exclude_unless:type,'.Post::TYPE_VIDEO,
                'required_if:type,'.Post::TYPE_VIDEO,
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $media = Media::where('hash', $value)->first();
                    if (! $media) {
                        $fail('The selected video was not found. Upload the video first via /api/v1/user/media.');

                        return;
                    }
                    if (! str_starts_with($media->mime_type ?? '', 'video/')) {
                        $fail('The selected media is not a video.');
                    }
                    if ($media->entity_id !== null) {
                        $fail('This video is already attached to another post.');
                    }
                },
            ],
            'thumbnail_hash' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $media = Media::where('hash', $value)->first();
                    if (! $media) {
                        $fail('The selected thumbnail was not found. Upload the image first via /api/v1/user/media.');

                        return;
                    }
                    if (! str_starts_with($media->mime_type ?? '', 'image/')) {
                        $fail('The selected media is not an image.');
                    }
                    if ($media->entity_id !== null) {
                        $fail('This media is already attached to another entity.');
                    }
                },
            ],
            'image_hashes' => [
                'exclude_unless:type,'.Post::TYPE_GALLERY,
                'required_if:type,'.Post::TYPE_GALLERY,
                'array',
                'min:1',
            ],
            'image_hashes.*' => [
                'required',
                'string',
                'distinct',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $media = Media::where('hash', $value)->first();
                    if (! $media) {
                        $fail('The selected image was not found. Upload the image first via /api/v1/user/media.');

                        return;
                    }
                    if (! str_starts_with($media->mime_type ?? '', 'image/')) {
                        $fail('The selected media is not an image.');
                    }
                    if ($media->entity_id !== null) {
                        $fail('This image is already attached to another entity.');
                    }
                },
            ],
            'state' => ['nullable', 'integer', 'in:'.implode(',', Post::STATES)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('type')) {
            $this->merge(['type' => Post::TYPE_VIDEO]);
        }
    }
}

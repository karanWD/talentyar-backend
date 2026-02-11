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
            'caption' => ['required', 'string', 'max:5000'],
            'video_hash' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $media = Media::where('hash', $value)->first();
                    if (!$media) {
                        $fail('The selected video was not found. Upload the video first via /api/v1/user/media.');
                        return;
                    }
                    if (!str_starts_with($media->mime_type ?? '', 'video/')) {
                        $fail('The selected media is not a video.');
                    }
                    if ($media->entity_id !== null) {
                        $fail('This video is already attached to another post.');
                    }
                },
            ],
            'state' => ['nullable', 'integer', 'in:' . implode(',', Post::STATES)],
        ];
    }
}

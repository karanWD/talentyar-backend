<?php

namespace App\Http\Requests;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileAvatarRequest extends FormRequest
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
     * All fields are nullable for partial profile updates.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profile_hash' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $media = Media::where('hash', $value)->first();
                    if (!$media) {
                        $fail('The selected file was not found. Upload the file first via /api/v1/user/media.');
                        return;
                    }
                    if (!str_starts_with($media->mime_type ?? '', 'image/')) {
                        $fail('The selected media is not a photo.');
                    }
//                    if ($media->entity_id !== null) {
//                        $fail('This file is already attached to another entity.');
//                    }
                },
            ],
        ];
    }
}

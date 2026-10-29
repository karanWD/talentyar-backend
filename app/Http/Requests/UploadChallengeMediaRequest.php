<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadChallengeMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:'.UploadMediaRequest::IMAGE_MIMES.','.UploadMediaRequest::VIDEO_MIMES,
                'max:'.(int) ceil(UploadMediaRequest::maxUploadBytes() / 1024),
            ],
            'alt' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxMb = (int) (UploadMediaRequest::maxUploadBytes() / (1024 * 1024));

        return [
            'file.max' => "The file must not be larger than {$maxMb} MB.",
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    /**
     * Allowed image mimes for upload.
     */
    public const IMAGE_MIMES = 'jpeg,jpg,png,gif,webp';

    /**
     * Allowed video mimes (extensions) for upload.
     */
    public const VIDEO_MIMES = 'mp4,mov,avi,webm,mpeg';

    /**
     * Max file size for images (bytes) = 10MB.
     */
    public const IMAGE_MAX_SIZE = 10 * 1024 * 1024;

    /**
     * Max video/file size from config (supports large videos, e.g. 400MB via MEDIA_MAX_UPLOAD_MB).
     */
    public static function maxUploadBytes(): int
    {
        return (int) config('media.max_upload_bytes', 100 * 1024 * 1024);
    }

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
            'file' => [
                'required',
                'file',
                'mimes:' . self::IMAGE_MIMES . ',' . self::VIDEO_MIMES,
                'max:' . self::maxUploadBytes(),
            ],
            'type' => ['nullable', 'string', 'in:' . implode(',', Media::TYPE)],
            'alt' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxMb = (int) (self::maxUploadBytes() / (1024 * 1024));

        return [
            'file.max' => "The file must not be larger than {$maxMb} MB.",
        ];
    }
}

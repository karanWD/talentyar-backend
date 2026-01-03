<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Media;

class UploadMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api-employer')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:204800',
            'entity_slug' => 'required|string',
            'type' => 'nullable|string|in:'.implode(',', Media::TYPE),
            'alt' => 'nullable|string',
            'meta_data' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'files.required' => 'The files are required.',
            'files.array' => 'The files must be an array.',
            'files.*.required' => 'The file is required.',
            'files.*.file' => 'The file must be a file.',
            'files.*.mimes' => 'The file must be a valid image or pdf.',
            'files.*.max' => 'The file must be less than 204800 bytes.',
            'entity_slug.required' => 'The entity slug is required.',
            'entity_slug.string' => 'The entity slug must be a string.',
            'type.nullable' => 'The type is nullable.',
            'type.string' => 'The type must be a string.',
            'type.in' => 'The type must be a valid type.',
            'alt.nullable' => 'The alt is nullable.',
            'alt.string' => 'The alt must be a string.',
            'meta_data.nullable' => 'The meta data is nullable.',
            'meta_data.array' => 'The meta data must be an array.',
        ];
    }
}


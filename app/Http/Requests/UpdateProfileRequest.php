<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^09[0-9]{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'gender' => ['nullable', 'integer', 'in:'.implode(',', User::GENDERS)],
            'birth_date' => ['nullable', 'string', 'date'],
            'weight' => ['nullable', 'integer', 'min:0'],
            'height' => ['nullable', 'integer', 'min:0'],
            'foot_specialization' => ['nullable', 'string', 'in:'.implode(',', User::FOOT_SPECIALIZATION)],
            'post_skill' => ['nullable', 'string', 'in:'.implode(',', User::POST_SKILL)],
            'skill_level' => ['nullable', 'string', 'in:'.implode(',', User::SKILL_LEVEL)],
            'activity_history' => ['nullable', 'boolean'],
            'team_name' => ['nullable', 'string', 'max:255'],
            'favorite_iranian_team' => ['nullable', 'string', 'max:255'],
            'favorite_foreign_team' => ['nullable', 'string', 'max:255'],
            'shirt_number' => ['nullable', 'integer', 'min:0'],
            'bio' => ['nullable', 'string'],
        ];
    }
}

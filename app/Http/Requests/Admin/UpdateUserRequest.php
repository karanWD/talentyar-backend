<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        /** @var User $user */
        $user = $this->route('user');

        return [
            'phone' => [
                'sometimes',
                'string',
                'regex:/^09[0-9]{9}$/',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'first_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'province_id' => ['sometimes', 'nullable', 'integer', 'exists:provinces,id'],
            'city_id' => ['sometimes', 'nullable', 'integer', 'exists:cities,id'],
            'gender' => ['sometimes', 'nullable', 'integer', 'in:' . implode(',', User::GENDERS)],
            'birth_date' => ['sometimes', 'nullable', 'string', 'date'],
            'weight' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'height' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'foot_specialization' => ['sometimes', 'nullable', 'string', 'in:' . implode(',', User::FOOT_SPECIALIZATION)],
            'post_skill' => ['sometimes', 'nullable', 'string', 'in:' . implode(',', User::POST_SKILL)],
            'skill_level' => ['sometimes', 'nullable', 'string', 'in:' . implode(',', User::SKILL_LEVEL)],
            'activity_history' => ['sometimes', 'nullable', 'boolean'],
            'team_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'favorite_iranian_team' => ['sometimes', 'nullable', 'string', 'max:255'],
            'favorite_foreign_team' => ['sometimes', 'nullable', 'string', 'max:255'],
            'shirt_number' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'bio' => ['sometimes', 'nullable', 'string'],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Services\AdminActivityFieldService;
use App\Services\AdminPermissionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $admin = $this->user('api-admin');

        return $admin instanceof Admin
            && app(AdminPermissionService::class)->can($admin, 'create-users');
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Admin $admin */
        $admin = $this->user('api-admin');

        return [
            'phone' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users,phone'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
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
            'activity_field_ids' => ['required', 'array', 'min:1'],
            'activity_field_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::in(app(AdminActivityFieldService::class)->ids($admin)),
            ],
        ];
    }
}

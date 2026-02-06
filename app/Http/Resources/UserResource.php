<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    /**
     * Get the resource data array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
            'phone' => $this->phone,
            'email' => $this->email,
            'username' => $this->username,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'weight' => $this->weight,
            'height' => $this->height,
            'foot_specialization' => $this->foot_specialization,
            'post_skill' => $this->post_skill,
            'skill_level' => $this->skill_level,
            'activity_history' => $this->activity_history,
            'team_name' => $this->team_name,
            'favorite_iranian_team' => $this->favorite_iranian_team,
            'favorite_foreign_team' => $this->favorite_foreign_team,
            'shirt_number' => $this->shirt_number,
            'bio' => $this->bio,
            ...$this->includeTimestamps(),
        ];
    }
}


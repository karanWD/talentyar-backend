<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserActivityFieldSkillResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'activity_field_id' => $this->activity_field_id,
            'answers' => $this->answers ?? [],
            'activity_field' => $this->whenLoaded('activityField', function () use ($request) {
                return (new ActivityFieldResource($this->activityField))->toArray($request);
            }),
            ...$this->includeTimestamps(),
        ];
    }
}

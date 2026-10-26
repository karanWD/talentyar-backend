<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ChallengeResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'activity_field_id' => $this->activity_field_id,
            'activity_field' => $this->relationLoaded('activityField')
                ? new ActivityFieldResource($this->activityField)
                : null,
            'title' => $this->title,
            'description' => $this->description,
            'about' => $this->about,
            'conditions' => $this->conditions,
            'rules' => $this->rules,
            'prize' => $this->prize,
            'status' => $this->status,
            'active_duration_days' => $this->active_duration_days,
            'activated_at' => $this->activated_at?->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'is_currently_active' => $this->isCurrentlyActive(),
            'training_video' => $this->relationLoaded('trainingVideo') && $this->trainingVideo
                ? new MediaResource($this->trainingVideo)
                : null,
            ...$this->includeTimestamps(),
        ];
    }
}

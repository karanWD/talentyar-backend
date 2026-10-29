<?php

namespace App\Http\Resources;

use App\Models\User;
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
            'type' => $this->type?->value,
            'action_type' => $this->action_type?->value,
            'action_target_count' => $this->action_target_count,
            'status' => $this->status,
            'active_duration_days' => $this->active_duration_days,
            'activated_at' => $this->activated_at?->toIso8601String(),
            'expires_at' => $this->expires_at?->toIso8601String(),
            'is_currently_active' => $this->isCurrentlyActive(),
            'participants_count' => $this->when(
                array_key_exists('participations_count', $this->resource->getAttributes()),
                (int) ($this->participations_count ?? 0)
            ),
            'has_joined' => $this->when(
                $request->user() instanceof User,
                (bool) ($this->has_joined ?? false)
            ),
            'can_join' => $this->when(
                $request->user() instanceof User,
                (bool) ($this->can_join ?? false)
            ),
            'training_video' => $this->relationLoaded('trainingVideo') && $this->trainingVideo
                ? new MediaResource($this->trainingVideo)
                : null,
            ...$this->includeTimestamps(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class EvaluationResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'activity_fields_id' => $this->activity_fields_id,
            'score' => $this->score,
            'pros_comment' => $this->pros_comment,
            'cons_comment' => $this->cons_comment,
            'suggestion' => $this->suggestion,
            'summary' => $this->summary,
            'status' => $this->status,
            'user' => new UserResource($this->user),
            'media' => $this->whenLoaded('media', fn () => MediaResource::collection($this->media)),
            'jalali_date' => $this->created_at
                ? Jalalian::fromCarbon($this->created_at)->format('Y/m/d')
                : null,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($evaluationItem) => [
                'id' => $evaluationItem->id,
                'score' => (int) $evaluationItem->score,
                'item' => $evaluationItem->relationLoaded('item') && $evaluationItem->item ? [
                    'id' => $evaluationItem->item->id,
                    'name' => $evaluationItem->item->name,
                    'activity_field_id' => $evaluationItem->item->activity_field_id,
                ] : null,
            ])->values()),
            ...$this->includeTimestamps(),
        ];
    }
}

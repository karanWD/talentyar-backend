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
            'score' => $this->score,
            'pros_comment' => $this->pros_comment,
            'cons_comment' => $this->cons_comment,
            'status' => $this->status,
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
                    'type' => $evaluationItem->item->type,
                ] : null,
            ])->values()),
            ...$this->includeTimestamps(),
        ];
    }
}

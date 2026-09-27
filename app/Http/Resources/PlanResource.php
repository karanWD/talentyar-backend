<?php

namespace App\Http\Resources;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'id' => $this->id,
            'activity_field_id' => $this->activity_field_id,
            'name' => $this->name,
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price !== null ? (float) $this->discount_price : 0,
            're_evaluation_price' => (int) $this->re_evaluation_price,
            'duration' => (int) $this->duration / 30,
            'type' => $this->type,
            'type_label' => Plan::TYPE[$this->type] ?? $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'features' => collect($this->features ?? [])
                ->map(fn (array $feature) => [
                    'title' => $feature['title'] ?? null,
                    'type' => $feature['type'] ?? 'normal',
                    'is_special' => ($feature['type'] ?? 'normal') === 'special',
                ])
                ->values()
                ->all(),
            ...$this->includeTimestamps(),
        ];
    }
}

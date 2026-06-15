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
            'name' => $this->name,
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price !== null ? (float) $this->discount_price : 0,
            'duration' => (int) $this->duration,
            'type' => $this->type,
            'type_label' => Plan::TYPE[$this->type] ?? $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'features' => $this->features,
            ...$this->includeTimestamps(),
        ];
    }
}

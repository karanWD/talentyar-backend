<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SubscriptionResource extends BaseResource
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
            'plan' => new PlanResource($this->plan),
            'status' => $this->status,
            'price_paid' => $this->price_paid,
            'discount_price' => $this->discount_price,
            'started_at' => $this->started_at,
            'expires_at' => $this->expires_at,
            'cancelled_at' => $this->cancelled_at,
            'payment_reference' => $this->payment_reference,
            // Merge in the timestamps, if any
        ];
    }
}


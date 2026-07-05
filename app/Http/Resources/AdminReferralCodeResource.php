<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AdminReferralCodeResource extends BaseResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getResourceData(Request $request): array
    {
        return [
            'referral_code' => $this->referral_code,
            'used_count' => (int) ($this->referrals_count ?? 0),
            'owner' => [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'full_name' => trim("{$this->first_name} {$this->last_name}") ?: null,
                'username' => $this->username,
                'phone' => $this->phone,
                'email' => $this->email,
            ],
            ...$this->includeTimestamps(),
        ];
    }
}

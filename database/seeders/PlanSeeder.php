<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Duration is stored in months.
     */
    private const DURATIONS = [
        30 => '1 Month',
        180 => '6 Months',
        360 => '1 Year',
    ];

    /**
     * Base monthly price per plan type (before duration multiplier).
     */
    private const BASE_MONTHLY_PRICE = [
        Plan::TYPE_BASIC => 99_000,
        Plan::TYPE_PRO => 199_000,
        Plan::TYPE_VIP => 399_000,
    ];

    public function run(): void
    {
        foreach ([Plan::TYPE_BASIC, Plan::TYPE_PRO, Plan::TYPE_VIP] as $type) {
            foreach (self::DURATIONS as $months => $durationLabel) {
                $name = sprintf('%s-%d-month', $type, $months);
                $price = $this->priceFor($type, $months);

                Plan::updateOrCreate(
                    ['name' => $name],
                    [
                        'price' => $price,
                        'discount_price' => $this->discountPrice($price, $months),
                        'duration' => $months,
                        'type' => $type,
                        'status' => Plan::STATUS_ACTIVE,
                        'description' => sprintf(
                            '%s plan — %s (%d month%s).',
                            ucfirst($type),
                            $durationLabel,
                            $months,
                            $months === 1 ? '' : 's'
                        ),
                    ]
                );
            }
        }
    }

    private function priceFor(string $type, int $months): float
    {
        $base = self::BASE_MONTHLY_PRICE[$type];

        return match ($months) {
            1 => $base,
            6 => round($base * 6 * 0.9),
            12 => round($base * 12 * 0.8),
            default => $base * $months,
        };
    }

    private function discountPrice(float $price, int $months): ?float
    {
        return match ($months) {
            6 => round($price * 0.95, 2),
            12 => round($price * 0.9, 2),
            default => null,
        };
    }
}

<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    private const PLANS = [
        Plan::TYPE_BASIC => [
            'title' => 'پلن بیسیک',
            'subtitle' => 'برنامه‌ای اقتصادی مناسب برای شروع مسیر دیده شدن استعدادهات.',
            'prices' => [
                1 => 249_000,
                3 => 679_000,
                6 => 1_199_000,
                12 => 2_199_000,
            ],
            'features' => [
                'عدم محدودیت در بارگذاری فیلم',
                'آشنایی با استعدادهای برتر هر رشته',
                'ارزیابی استعدادت توسط سیستم هوشمند یک بار در ماه',
                'حضور در رتبه‌بندی‌های ارزیابی‌ها',
                'امکان شرکت در چالش‌ها و کسب جوایز',
                'دریافت نمودار رشد',
            ],
        ],
        Plan::TYPE_PRO => [
            'title' => 'پلن حرفه‌ای',
            'subtitle' => 'برنامه‌ای مناسب برای اینکه تو مسیرت پیشرفت کنی و بیشتر دیده شی',
            'prices' => [
                1 => 389_000,
                3 => 999_000,
                6 => 1_790_000,
                12 => 3_299_000,
            ],
            'features' => [
                'تحلیل و بررسی نقاط ضعف و قدرت',
                'کمک به دیده شدن بیشتر استعدادت',
                'عدم محدودیت در بارگذاری فیلم',
                'آشنایی با استعدادهای برتر هر رشته',
                'ارزیابی استعدادت توسط سیستم هوشمند یک بار در ماه',
                'حضور در رتبه‌بندی‌های ارزیابی‌ها',
                'امکان شرکت در چالش‌ها و کسب جوایز',
                'دریافت نمودار رشد',
            ],
        ],
        Plan::TYPE_VIP => [
            'title' => 'پلن VIP',
            'subtitle' => 'برنامه‌ای کامل تا سریع‌تر استعدادهات دیده و پرورش داده شه',
            'prices' => [
                1 => 999_000,
                3 => 2_690_000,
                6 => 4_899_000,
                12 => 9_299_000,
            ],
            'features' => [
                'آنالیز توسط مربیان و بازیکنان لیگ',
                'دریافت برنامه تمرینی',
                'مشاوره تلفنی با آنالیزور تیم',
                'کمک به دیده شدن بیشتر استعدادت',
                'تحلیل و بررسی نقاط ضعف و قدرت',
                'عدم محدودیت در بارگذاری فیلم',
                'آشنایی با استعدادهای برتر هر رشته',
                'ارزیابی استعدادت توسط سیستم هوشمند یک بار در ماه',
                'حضور در رتبه‌بندی‌های ارزیابی‌ها',
                'امکان شرکت در چالش‌ها و کسب جوایز',
                'دریافت نمودار رشد',
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::PLANS as $type => $planData) {
            foreach ($planData['prices'] as $duration => $price) {
                Plan::updateOrCreate(
                    ['name' => sprintf('%s-%d-month', $type, $duration)],
                    [
                        'price' => $price,
                        'discount_price' => null,
                        'duration' => $duration,
                        'type' => $type,
                        'status' => Plan::STATUS_ACTIVE,
                        'description' => $this->buildDescription($planData['title'], $planData['subtitle'], $planData['features']),
                    ]
                );
            }
        }
    }

    private function buildDescription(string $title, string $subtitle, array $features): string
    {
        return trim($title . PHP_EOL . $subtitle . PHP_EOL . PHP_EOL . implode(PHP_EOL, $features));
    }
}

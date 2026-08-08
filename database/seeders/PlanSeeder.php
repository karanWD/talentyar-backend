<?php

namespace Database\Seeders;

use App\Models\ActivityField;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    private static function plans(): array
    {
        return [
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
                    self::feature('عدم محدودیت در بارگذاری فیلم'),
                    self::feature('آشنایی با استعدادهای برتر هر رشته'),
                    self::feature('ارزیابی استعدادت توسط سیستم هوشمند یک بار در ماه'),
                    self::feature('حضور در رتبه‌بندی‌های ارزیابی‌ها'),
                    self::feature('امکان شرکت در چالش‌ها و کسب جوایز'),
                    self::feature('دریافت نمودار رشد'),
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
                    self::feature('تحلیل و بررسی نقاط ضعف و قدرت', 'special'),
                    self::feature('کمک به دیده شدن بیشتر استعدادت', 'special'),
                    self::feature('عدم محدودیت در بارگذاری فیلم'),
                    self::feature('آشنایی با استعدادهای برتر هر رشته'),
                    self::feature('ارزیابی استعدادت توسط سیستم هوشمند یک بار در ماه'),
                    self::feature('حضور در رتبه‌بندی‌های ارزیابی‌ها'),
                    self::feature('امکان شرکت در چالش‌ها و کسب جوایز'),
                    self::feature('دریافت نمودار رشد'),
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
                    self::feature('آنالیز توسط مربیان و بازیکنان لیگ', 'special'),
                    self::feature('دریافت برنامه تمرینی', 'special'),
                    self::feature('مشاوره تلفنی با آنالیزور تیم', 'special'),
                    self::feature('کمک به دیده شدن بیشتر استعدادت'),
                    self::feature('تحلیل و بررسی نقاط ضعف و قدرت'),
                    self::feature('عدم محدودیت در بارگذاری فیلم'),
                    self::feature('آشنایی با استعدادهای برتر هر رشته'),
                    self::feature('ارزیابی استعدادت توسط سیستم هوشمند یک بار در ماه'),
                    self::feature('حضور در رتبه‌بندی‌های ارزیابی‌ها'),
                    self::feature('امکان شرکت در چالش‌ها و کسب جوایز'),
                    self::feature('دریافت نمودار رشد'),
                ],
            ],
        ];
    }

    public function run(): void
    {
        ActivityField::query()->each(function (ActivityField $activityField): void {
            foreach (self::plans() as $type => $planData) {
                foreach ($planData['prices'] as $duration => $price) {
                    Plan::updateOrCreate(
                        [
                            'activity_field_id' => $activityField->id,
                            'name' => sprintf('%s-%d-month', $type, $duration),
                        ],
                        [
                            'price' => $price,
                            'discount_price' => null,
                            'duration' => $duration,
                            'type' => $type,
                            'status' => Plan::STATUS_ACTIVE,
                            'description' => $this->buildDescription($planData['title'], $planData['subtitle']),
                            'features' => $planData['features'],
                        ]
                    );
                }
            }
        });
    }

    private static function feature(string $title, string $type = 'normal'): array
    {
        return [
            'title' => $title,
            'type' => $type,
        ];
    }

    private function buildDescription(string $title, string $subtitle): string
    {
        return trim($title.PHP_EOL.$subtitle.PHP_EOL);
    }
}

<?php

namespace Database\Seeders;

use App\Models\ActivityField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActivityFieldSeeder extends Seeder
{
    private const FIELDS = [
        'فوتبال',
        'بسکتبال',
        'بوکس',
        'بدمینتون',
        'استندآپ کمدی',
        'تکواندو',
        'پیانو',
        'پدل',
        'بازیگری',
        'دومیدانی',
        'تنیس',
        'تیراندازی',
        'تنیس روی میز',
        'خوانندگی',
        'شنا',
        'ژیمناستیک',
        'سه تار',
        'کارگردانی',
        'کاراته',
        'فوتسال',
        'شیرجه',
        'گیتار',
        'مجری گری',
        'گزارشگری',
        'کشتی',
        'ووشو',
        'وزنه برداری',
        'هندل بال',
        'واترپلو',
        'ویولن',
        'والیبال',
    ];

    public function run(): void
    {
        foreach (self::FIELDS as $fieldName) {
            ActivityField::updateOrCreate(
                ['name' => $fieldName],
                ['slug' => Str::slug($fieldName)]
            );
        }
    }
}

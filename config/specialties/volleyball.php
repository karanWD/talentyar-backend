<?php

return [
    'title' => 'والیبال',
    'slug' => 'volleyball',

    'sections' => [
        [
            'key' => 'personal_information',
            'title' => 'مشخصات فردی',
            'questions' => [
                [
                    'key' => 'height',
                    'label' => 'قد',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'under_160' => 'کمتر از ۱۶۰ سانتی‌متر',
                        '160_to_170' => '۱۶۰ تا ۱۷۰ سانتی‌متر',
                        '170_to_180' => '۱۷۰ تا ۱۸۰ سانتی‌متر',
                        '180_to_190' => '۱۸۰ تا ۱۹۰ سانتی‌متر',
                        '190_to_200' => '۱۹۰ تا ۲۰۰ سانتی‌متر',
                        'over_200' => 'بیشتر از ۲۰۰ سانتی‌متر',
                    ],
                ],
                [
                    'key' => 'weight',
                    'label' => 'وزن',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'under_50' => 'کمتر از ۵۰ کیلوگرم',
                        '50_to_60' => '۵۰ تا ۶۰ کیلوگرم',
                        '60_to_70' => '۶۰ تا ۷۰ کیلوگرم',
                        '70_to_80' => '۷۰ تا ۸۰ کیلوگرم',
                        '80_to_90' => '۸۰ تا ۹۰ کیلوگرم',
                        'over_90' => 'بیشتر از ۹۰ کیلوگرم',
                    ],
                ],
                [
                    'key' => 'dominant_hand',
                    'label' => 'دست غالب',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'right' => 'راست',
                        'left' => 'چپ',
                    ],
                ],
                [
                    'key' => 'vertical_jump_height',
                    'label' => 'ارتفاع پرش',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'under_40' => 'کمتر از ۴۰ سانتی‌متر',
                        '40_to_60' => '۴۰ تا ۶۰ سانتی‌متر',
                        '60_to_80' => '۶۰ تا ۸۰ سانتی‌متر',
                        '80_to_100' => '۸۰ تا ۱۰۰ سانتی‌متر',
                        'over_100' => 'بیشتر از ۱۰۰ سانتی‌متر',
                    ],
                ],
                [
                    'key' => 'standing_reach',
                    'label' => 'فاصله نوک دست در حالت دست‌های کشیده',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'under_160' => 'کمتر از ۱۶۰ سانتی‌متر',
                        '160_to_170' => '۱۶۰ تا ۱۷۰ سانتی‌متر',
                        '170_to_180' => '۱۷۰ تا ۱۸۰ سانتی‌متر',
                        '180_to_190' => '۱۸۰ تا ۱۹۰ سانتی‌متر',
                        '190_to_200' => '۱۹۰ تا ۲۰۰ سانتی‌متر',
                        'over_200' => 'بیشتر از ۲۰۰ سانتی‌متر',
                    ],
                ],
            ],
        ],

        [
            'key' => 'volleyball_information',
            'title' => 'مشخصات تخصصی والیبال',
            'questions' => [
                [
                    'key' => 'playing_position',
                    'label' => 'پست تخصصی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'setter' => 'پاسور',
                        'libero' => 'لیبرو',
                        'outside_hitter' => 'دریافت‌کننده قدرتی',
                        'middle_blocker' => 'سرعتی‌زن',
                        'opposite' => 'قطر پاسور',
                    ],
                ],
                [
                    'key' => 'playing_level',
                    'label' => 'سطح بازی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'beginner' => 'مبتدی',
                        'semi_professional' => 'نیمه‌حرفه‌ای',
                        'professional' => 'حرفه‌ای',
                        'academy' => 'آکادمی',
                        'national' => 'سطح ملی',
                    ],
                ],
                [
                    'key' => 'activity_experience',
                    'label' => 'سابقه فعالیت',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'under_1_year' => 'کمتر از ۱ سال',
                        '1_to_3_years' => '۱ تا ۳ سال',
                        '3_to_5_years' => '۳ تا ۵ سال',
                        '5_to_10_years' => '۵ تا ۱۰ سال',
                        'over_10_years' => 'بیشتر از ۱۰ سال',
                    ],
                ],
                [
                    'key' => 'competition_level',
                    'label' => 'سطح مسابقات',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'school' => 'مدرسه',
                        'club' => 'باشگاهی',
                        'provincial' => 'استانی',
                        'national' => 'کشوری',
                        'international' => 'بین‌المللی',
                    ],
                ],
                [
                    'key' => 'current_club',
                    'label' => 'باشگاه فعلی',
                    'type' => 'text',
                    'required' => false,
                    'validation' => [
                        'string',
                        'max:255',
                    ],
                ],
            ],
        ],
    ],
];

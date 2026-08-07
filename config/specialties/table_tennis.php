<?php

return [
    'title' => 'تنیس روی میز',
    'slug' => 'table_tennis',

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
                        'under_140' => 'کمتر از ۱۴۰ سانتی‌متر',
                        '140_to_150' => '۱۴۰ تا ۱۵۰ سانتی‌متر',
                        '150_to_160' => '۱۵۰ تا ۱۶۰ سانتی‌متر',
                        '160_to_170' => '۱۶۰ تا ۱۷۰ سانتی‌متر',
                        '170_to_180' => '۱۷۰ تا ۱۸۰ سانتی‌متر',
                        'over_180' => 'بیشتر از ۱۸۰ سانتی‌متر',
                    ],
                ],
                [
                    'key' => 'weight',
                    'label' => 'وزن',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'under_40' => 'کمتر از ۴۰ کیلوگرم',
                        '40_to_50' => '۴۰ تا ۵۰ کیلوگرم',
                        '50_to_60' => '۵۰ تا ۶۰ کیلوگرم',
                        '60_to_70' => '۶۰ تا ۷۰ کیلوگرم',
                        '70_to_80' => '۷۰ تا ۸۰ کیلوگرم',
                        'over_80' => 'بیشتر از ۸۰ کیلوگرم',
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
            ],
        ],

        [
            'key' => 'table_tennis_information',
            'title' => 'مشخصات تخصصی تنیس روی میز',
            'questions' => [
                [
                    'key' => 'playing_style',
                    'label' => 'سبک بازی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'offensive' => 'هجومی',
                        'defensive' => 'دفاعی',
                        'all_round' => 'آل‌راند',
                    ],
                ],
                [
                    'key' => 'racket_grip',
                    'label' => 'نحوه گرفتن راکت',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'shakehand' => 'شیک‌هند (Shakehand)',
                        'penhold' => 'پن‌هولد (Penhold)',
                    ],
                ],
                [
                    'key' => 'playing_level',
                    'label' => 'سطح بازی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'beginner' => 'مبتدی',
                        'academy' => 'آکادمی',
                        'professional' => 'حرفه‌ای',
                        'provincial' => 'استانی',
                        'national' => 'کشوری',
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

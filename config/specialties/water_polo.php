<?php

return [
    'title' => 'واترپلو',
    'slug' => 'water_polo',

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
                        'under_150' => 'کمتر از ۱۵۰ سانتی‌متر',
                        '150_to_160' => '۱۵۰ تا ۱۶۰ سانتی‌متر',
                        '160_to_170' => '۱۶۰ تا ۱۷۰ سانتی‌متر',
                        '170_to_180' => '۱۷۰ تا ۱۸۰ سانتی‌متر',
                        '180_to_190' => '۱۸۰ تا ۱۹۰ سانتی‌متر',
                        'over_190' => 'بیشتر از ۱۹۰ سانتی‌متر',
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
                    'key' => 'arm_length',
                    'label' => 'طول دست‌ها',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'short' => 'کوتاه',
                        'medium' => 'متوسط',
                        'long' => 'بلند',
                    ],
                ],
                [
                    'key' => 'endurance',
                    'label' => 'استقامت',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'weak' => 'ضعیف',
                        'medium' => 'متوسط',
                        'good' => 'خوب',
                        'excellent' => 'عالی',
                    ],
                ],
                [
                    'key' => 'physical_strength',
                    'label' => 'قدرت بدنی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'weak' => 'ضعیف',
                        'medium' => 'متوسط',
                        'good' => 'خوب',
                        'excellent' => 'عالی',
                    ],
                ],
            ],
        ],

        [
            'key' => 'water_polo_information',
            'title' => 'مشخصات تخصصی واترپلو',
            'questions' => [
                [
                    'key' => 'playing_position',
                    'label' => 'پست تخصصی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'goalkeeper' => 'دروازه‌بان',
                        'center_forward' => 'سنتر فوروارد',
                        'center_back' => 'سنتر بک',
                        'winger' => 'وینگر',
                        'driver' => 'درایور',
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
                        'over_10_years' => 'بیش از ۱۰ سال',
                    ],
                ],
                [
                    'key' => 'competition_level',
                    'label' => 'سطح مسابقات',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
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

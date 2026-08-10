<?php

return [
    'title' => 'هندبال',
    'slug' => 'handball',

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
                    'key' => 'vertical_jump_height',
                    'label' => 'ارتفاع پرش',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'under_30' => 'کمتر از ۳۰ سانتی‌متر',
                        '30_to_50' => '۳۰ تا ۵۰ سانتی‌متر',
                        '50_to_70' => '۵۰ تا ۷۰ سانتی‌متر',
                        'over_70' => 'بیشتر از ۷۰ سانتی‌متر',
                    ],
                ],
                [
                    'key' => 'arm_length',
                    'label' => 'طول دست',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'short' => 'کوتاه',
                        'medium' => 'متوسط',
                        'long' => 'بلند',
                    ],
                ],
            ],
        ],

        [
            'key' => 'handball_information',
            'title' => 'مشخصات تخصصی هندبال',
            'questions' => [
                [
                    'key' => 'playing_position',
                    'label' => 'پست تخصصی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'goalkeeper' => 'دروازه‌بان',
                        'left_wing' => 'وینگر چپ',
                        'right_wing' => 'وینگر راست',
                        'left_back' => 'بک چپ',
                        'right_back' => 'بک راست',
                        'center_back' => 'سنتر',
                        'pivot' => 'پیوت',
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
                        'provincial_league' => 'لیگ استان',
                        'national_league' => 'لیگ کشور',
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

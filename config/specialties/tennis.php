<?php

return [
    'title' => 'تنیس',
    'slug' => 'tennis',

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
                [
                    'key' => 'reaction_speed',
                    'label' => 'سرعت واکنش',
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
                    'key' => 'flexibility',
                    'label' => 'انعطاف بدنی',
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
            'key' => 'tennis_information',
            'title' => 'اطلاعات تخصصی تنیس',
            'questions' => [
                [
                    'key' => 'playing_level',
                    'label' => 'سطح بازی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'beginner' => 'مبتدی',
                        'semi_professional' => 'نیمه‌حرفه‌ای',
                        'professional' => 'حرفه‌ای',
                        'tournament' => 'سطح تورنمنت',
                    ],
                ],
                [
                    'key' => 'playing_experience',
                    'label' => 'سابقه بازی',
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
                    'key' => 'backhand_type',
                    'label' => 'نوع بک‌هند',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'one_handed' => 'یک‌دست',
                        'two_handed' => 'دودست',
                    ],
                ],
                [
                    'key' => 'playing_style',
                    'label' => 'سبک بازی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'offensive' => 'هجومی',
                        'defensive' => 'دفاعی',
                        'all_court' => 'آل‌کورت',
                        'counter_puncher' => 'کانترپانچر',
                    ],
                ],
                [
                    'key' => 'preferred_court_surface',
                    'label' => 'زمین تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'hard' => 'هارد',
                        'clay' => 'خاک',
                        'grass' => 'چمن',
                    ],
                ],
            ],
        ],

        [
            'key' => 'experience_and_achievements',
            'title' => 'تجربه و افتخارات',
            'questions' => [
                [
                    'key' => 'competition_achievement_level',
                    'label' => 'مقام مسابقات',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'school' => 'مدرسه',
                        'provincial' => 'استانی',
                        'national' => 'کشوری',
                        'international' => 'بین‌المللی',
                    ],
                ],
                [
                    'key' => 'professional_tryout_experience',
                    'label' => 'تجربه تست حرفه‌ای',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'once' => '۱ بار',
                        '2_to_5_times' => '۲ تا ۵ بار',
                        'over_5_times' => 'بیشتر از ۵ بار',
                    ],
                ],
            ],
        ],
    ],
];

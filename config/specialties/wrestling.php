<?php

return [
    'title' => 'کشتی',
    'slug' => 'wrestling',

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
            ],
        ],

        [
            'key' => 'wrestling_information',
            'title' => 'اطلاعات تخصصی کشتی',
            'questions' => [
                [
                    'key' => 'wrestling_style',
                    'label' => 'سبک تخصصی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'freestyle' => 'آزاد',
                        'greco_roman' => 'فرنگی',
                    ],
                ],
                [
                    'key' => 'activity_level',
                    'label' => 'سطح فعالیت',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'beginner' => 'مبتدی',
                        'semi_professional' => 'نیمه‌حرفه‌ای',
                        'professional' => 'حرفه‌ای',
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
                        'club' => 'باشگاهی',
                        'provincial' => 'استانی',
                        'national' => 'کشوری',
                        'international' => 'بین‌المللی',
                    ],
                ],
            ],
        ],

        [
            'key' => 'experience_and_achievements',
            'title' => 'تجربه و افتخارات',
            'questions' => [
                [
                    'key' => 'most_important_achievement',
                    'label' => 'مهم‌ترین افتخار',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'club_championship' => 'قهرمانی باشگاهی',
                        'provincial_championship' => 'قهرمانی استانی',
                        'national_championship' => 'قهرمانی کشوری',
                        'international_championship' => 'قهرمانی بین‌المللی',
                    ],
                ],
                [
                    'key' => 'selected_team_invitation',
                    'label' => 'دعوت به تیم منتخب',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'نداشته‌ام',
                        'club' => 'باشگاهی',
                        'provincial' => 'استان',
                        'national' => 'کشور',
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
                [
                    'key' => 'official_match_experience',
                    'label' => 'تجربه مبارزه رسمی',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                    ],
                ],
            ],
        ],
    ],
];

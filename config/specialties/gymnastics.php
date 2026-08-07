<?php

return [
    'title' => 'ژیمناستیک',
    'slug' => 'gymnastics',

    'sections' => [
        [
            'key' => 'gymnastics_information',
            'title' => 'اطلاعات تخصصی ژیمناستیک',
            'questions' => [
                [
                    'key' => 'specialized_disciplines',
                    'label' => 'رشته تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'artistic_gymnastics' => 'ژیمناستیک هنری',
                        'rhythmic_gymnastics' => 'ژیمناستیک ریتمیک',
                        'trampoline' => 'ترامپولین',
                        'acrobatics' => 'آکروباتیک',
                        'aerobic_gymnastics' => 'ژیمناستیک ایروبیک',
                        'parkour' => 'پارکور',
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
                    'key' => 'training_experience',
                    'label' => 'سابقه تمرین',
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
                    'key' => 'training_level',
                    'label' => 'سطح آموزش',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'club' => 'باشگاهی',
                        'professional_academy' => 'آکادمی حرفه‌ای',
                        'provincial_team' => 'تیم استان',
                        'national_team' => 'تیم ملی',
                    ],
                ],
            ],
        ],

        [
            'key' => 'experience_and_achievements',
            'title' => 'تجربه و افتخارات',
            'questions' => [
                [
                    'key' => 'competition_experience',
                    'label' => 'تجربه مسابقات',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'none' => 'ندارم',
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                    ],
                ],
                [
                    'key' => 'competitions_count',
                    'label' => 'تعداد مسابقات',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        '0_to_5' => 'تا ۵ مسابقه',
                        '5_to_20' => '۵ تا ۲۰ مسابقه',
                        '20_to_50' => '۲۰ تا ۵۰ مسابقه',
                        'over_50' => 'بیش از ۵۰ مسابقه',
                    ],
                ],
                [
                    'key' => 'highest_competition_level',
                    'label' => 'بالاترین سطح مسابقات',
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
                    'key' => 'most_important_achievement',
                    'label' => 'مهم‌ترین افتخار',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'provincial_medal' => 'مدال استانی',
                        'national_medal' => 'مدال کشوری',
                        'international_medal' => 'مدال بین‌المللی',
                    ],
                ],
            ],
        ],
    ],
];

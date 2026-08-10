<?php

return [
    'title' => 'گزارشگری',
    'slug' => 'commentary',

    'sections' => [
        [
            'key' => 'commentary_information',
            'title' => 'اطلاعات تخصصی گزارشگری',
            'questions' => [
                [
                    'key' => 'specialized_fields',
                    'label' => 'حوزه تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'football' => 'فوتبال',
                        'basketball' => 'بسکتبال',
                        'volleyball' => 'والیبال',
                        'tennis' => 'تنیس',
                        'martial_arts' => 'ورزش‌های رزمی',
                        'general' => 'عمومی',
                    ],
                ],
                [
                    'key' => 'commentary_style',
                    'label' => 'سبک گزارش',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'exciting' => 'هیجانی',
                        'analytical' => 'تحلیلی',
                        'humorous' => 'طنز',
                        'formal' => 'رسمی',
                        'combined' => 'ترکیبی',
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
                        'media' => 'سطح رسانه‌ای',
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
                    'key' => 'performance_types',
                    'label' => 'نوع اجرا',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'live' => 'زنده',
                        'recorded' => 'ضبطی',
                        'studio' => 'استودیویی',
                        'match_commentary' => 'گزارش مسابقه',
                        'match_analysis' => 'تحلیل مسابقه',
                    ],
                ],
                [
                    'key' => 'performances_count',
                    'label' => 'تعداد اجراهای انجام‌شده',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        '0_to_10' => 'تا ۱۰ اجرا',
                        '10_to_50' => '۱۰ تا ۵۰ اجرا',
                        '50_to_100' => '۵۰ تا ۱۰۰ اجرا',
                        'over_100' => 'بیشتر از ۱۰۰ اجرا',
                    ],
                ],
            ],
        ],

        [
            'key' => 'sports_knowledge',
            'title' => 'دانش ورزشی',
            'questions' => [
                [
                    'key' => 'rules_knowledge',
                    'label' => 'دانش قوانین رشته',
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
                    'key' => 'tactical_knowledge',
                    'label' => 'دانش تاکتیکی',
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
                    'key' => 'historical_knowledge',
                    'label' => 'اطلاعات تاریخی',
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
                    'key' => 'teams_and_players_knowledge',
                    'label' => 'شناخت بازیکنان و تیم‌ها',
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
                    'key' => 'match_analysis_skill',
                    'label' => 'توانایی تحلیل مسابقه',
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
            'key' => 'language_and_dialect',
            'title' => 'زبان و گویش',
            'questions' => [
                [
                    'key' => 'native_dialect',
                    'label' => 'گویش اصلی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'tehrani' => 'تهرانی',
                        'turkish' => 'ترکی',
                        'kurdish' => 'کردی',
                        'lori' => 'لری',
                        'gilaki' => 'گیلکی',
                        'mazandarani' => 'مازنی',
                        'arabic' => 'عربی',
                        'other' => 'سایر',
                    ],
                ],
                [
                    'key' => 'accent_performance_skill',
                    'label' => 'توانایی اجرای لهجه',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'professional' => 'حرفه‌ای',
                    ],
                ],
                [
                    'key' => 'performance_languages',
                    'label' => 'زبان‌های قابل اجرا',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'persian' => 'فارسی',
                        'english' => 'انگلیسی',
                        'turkish' => 'ترکی',
                        'arabic' => 'عربی',
                        'french' => 'فرانسوی',
                        'other' => 'سایر',
                    ],
                ],
                [
                    'key' => 'second_language_level',
                    'label' => 'سطح زبان دوم',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'beginner' => 'مقدماتی',
                        'intermediate' => 'متوسط',
                        'advanced' => 'پیشرفته',
                    ],
                ],
            ],
        ],

        [
            'key' => 'experience_and_achievements',
            'title' => 'تجربه و افتخارات',
            'questions' => [
                [
                    'key' => 'official_media_experience',
                    'label' => 'تجربه حضور در رسانه رسمی',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                    ],
                ],
                [
                    'key' => 'official_match_commentary_experience',
                    'label' => 'تجربه گزارش مسابقه رسمی',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
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

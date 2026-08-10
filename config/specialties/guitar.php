<?php

return [
    'title' => 'گیتار',
    'slug' => 'guitar',

    'sections' => [
        [
            'key' => 'general_information',
            'title' => 'اطلاعات عمومی',
            'questions' => [
                [
                    'key' => 'guitar_types',
                    'label' => 'نوع گیتار تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'classical' => 'کلاسیک',
                        'acoustic' => 'آکوستیک',
                        'electric' => 'الکتریک',
                        'flamenco' => 'فلامنکو',
                        'bass' => 'بیس',
                    ],
                ],
                [
                    'key' => 'specialized_styles',
                    'label' => 'سبک تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'pop' => 'پاپ',
                        'rock' => 'راک',
                        'blues' => 'بلوز',
                        'jazz' => 'جاز',
                        'flamenco' => 'فلامنکو',
                        'classical' => 'کلاسیک',
                        'metal' => 'متال',
                        'fusion' => 'تلفیقی',
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
                        'stage' => 'سطح استیجی',
                    ],
                ],
                [
                    'key' => 'playing_experience',
                    'label' => 'سابقه نوازندگی',
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
                    'key' => 'education_level',
                    'label' => 'سطح آموزش',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'self_taught' => 'خودآموز',
                        'basic_classes' => 'کلاس مقدماتی',
                        'professional_institute' => 'آموزشگاه حرفه‌ای',
                        'conservatory' => 'کنسرواتوار',
                    ],
                ],
            ],
        ],

        [
            'key' => 'specialized_skills',
            'title' => 'مهارت‌های تخصصی',
            'questions' => [
                [
                    'key' => 'advanced_techniques_skill',
                    'label' => 'توانایی اجرای تکنیک‌های پیشرفته',
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
                    'key' => 'new_piece_learning_speed',
                    'label' => 'سرعت یادگیری قطعه جدید',
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
                    'key' => 'ensemble_playing_skill',
                    'label' => 'توانایی هم‌نوازی',
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
                    'key' => 'composition_skill',
                    'label' => 'توانایی آهنگ‌سازی',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'beginner' => 'مقدماتی',
                        'intermediate' => 'متوسط',
                        'professional' => 'حرفه‌ای',
                    ],
                ],
                [
                    'key' => 'music_arrangement_skill',
                    'label' => 'توانایی تنظیم موسیقی',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'beginner' => 'مقدماتی',
                        'intermediate' => 'متوسط',
                        'professional' => 'حرفه‌ای',
                    ],
                ],
            ],
        ],

        [
            'key' => 'experience_and_achievements',
            'title' => 'تجربه و افتخارات',
            'questions' => [
                [
                    'key' => 'stage_performance_experience',
                    'label' => 'تجربه اجرای صحنه‌ای',
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
                    'key' => 'studio_recording_experience',
                    'label' => 'تجربه ضبط استودیویی',
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
                    'key' => 'performances_count',
                    'label' => 'تعداد اجراها',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        '0_to_5' => 'تا ۵ اجرا',
                        '5_to_20' => '۵ تا ۲۰ اجرا',
                        '20_to_50' => '۲۰ تا ۵۰ اجرا',
                        'over_50' => 'بیشتر از ۵۰ اجرا',
                    ],
                ],
                [
                    'key' => 'most_important_achievement',
                    'label' => 'مهم‌ترین افتخار',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'local_festival' => 'جشنواره محلی',
                        'national_competition' => 'مسابقه کشوری',
                        'official_concert' => 'کنسرت رسمی',
                        'international' => 'افتخار بین‌المللی',
                    ],
                ],
            ],
        ],
    ],
];

<?php

return [
    'title' => 'پیانو',
    'slug' => 'piano',

    'sections' => [
        [
            'key' => 'general_information',
            'title' => 'اطلاعات عمومی',
            'questions' => [
                [
                    'key' => 'activity_level',
                    'label' => 'سطح فعالیت',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'beginner' => 'مبتدی',
                        'semi_professional' => 'نیمه‌حرفه‌ای',
                        'professional' => 'حرفه‌ای',
                        'stage' => 'سطح استیج',
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
                [
                    'key' => 'specialized_styles',
                    'label' => 'سبک تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'classical' => 'کلاسیک',
                        'jazz' => 'جاز',
                        'pop' => 'پاپ',
                        'blues' => 'بلوز',
                        'rock' => 'راک',
                        'film_music' => 'موسیقی فیلم',
                        'fusion' => 'تلفیقی',
                    ],
                ],
            ],
        ],

        [
            'key' => 'technical_skills',
            'title' => 'مهارت‌های فنی',
            'questions' => [
                [
                    'key' => 'sight_reading_skill',
                    'label' => 'نت‌خوانی',
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
                    'key' => 'hand_coordination_skill',
                    'label' => 'هماهنگی دو دست',
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
                    'key' => 'performance_accuracy',
                    'label' => 'دقت اجرا',
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
                    'key' => 'rhythm_and_tempo_control',
                    'label' => 'کنترل ریتم و تمپو',
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
                    'key' => 'scales_and_arpeggios_skill',
                    'label' => 'اجرای گام‌ها و آرپژها',
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
                    'key' => 'dynamics_control',
                    'label' => 'کنترل دینامیک',
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
                    'key' => 'pedal_usage_skill',
                    'label' => 'استفاده از پدال',
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
            'key' => 'specialized_skills',
            'title' => 'مهارت‌های تخصصی',
            'questions' => [
                [
                    'key' => 'complex_pieces_performance',
                    'label' => 'اجرای قطعات پیچیده',
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
                    'key' => 'improvisation_skill',
                    'label' => 'بداهه‌نوازی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'none' => 'ندارم',
                        'beginner' => 'مقدماتی',
                        'intermediate' => 'متوسط',
                        'professional' => 'حرفه‌ای',
                    ],
                ],
                [
                    'key' => 'ensemble_playing_skill',
                    'label' => 'هم‌نوازی',
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
                    'key' => 'composition_and_arrangement_skill',
                    'label' => 'آهنگ‌سازی و تنظیم',
                    'type' => 'single_select',
                    'required' => true,
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
                    'required' => true,
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
                    'required' => true,
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
                        'over_50' => 'بیش از ۵۰ اجرا',
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

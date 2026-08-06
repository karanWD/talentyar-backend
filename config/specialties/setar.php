<?php

return [
    'title' => 'سه‌تار',
    'slug' => 'setar',

    'sections' => [
        [
            'key' => 'personal_information',
            'title' => 'مشخصات فردی',
            'questions' => [
                [
                    'key' => 'age',
                    'label' => 'سن',
                    'type' => 'number',
                    'required' => true,
                    'validation' => [
                        'integer',
                        'min:5',
                        'max:100',
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
                    'key' => 'playing_experience',
                    'label' => 'سابقه نوازندگی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'no_experience' => 'بدون سابقه',
                        'under_1_year' => 'کمتر از ۱ سال',
                        '1_to_3_years' => '۱ تا ۳ سال',
                        '3_to_5_years' => '۳ تا ۵ سال',
                        'over_5_years' => 'بیش از ۵ سال',
                    ],
                ],
                [
                    'key' => 'education_level',
                    'label' => 'سطح آموزش',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'self_taught' => 'خودآموز',
                        'music_institute' => 'آموزشگاه',
                        'conservatory' => 'هنرستان',
                        'university' => 'دانشگاه',
                        'professional' => 'حرفه‌ای',
                    ],
                ],
            ],
        ],

        [
            'key' => 'specialized_information',
            'title' => 'مشخصات تخصصی',
            'questions' => [
                [
                    'key' => 'preferred_styles',
                    'label' => 'سبک مورد علاقه',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'traditional_music' => 'موسیقی سنتی',
                        'iranian_classical' => 'کلاسیک ایرانی',
                        'fusion' => 'تلفیقی',
                        'local_music' => 'موسیقی محلی',
                    ],
                ],
                [
                    'key' => 'stage_performance_experience',
                    'label' => 'تجربه اجرای صحنه‌ای',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'none' => 'ندارد',
                        'music_institute' => 'آموزشگاه',
                        'festival' => 'جشنواره',
                        'concert' => 'کنسرت',
                    ],
                ],
                [
                    'key' => 'competition_level',
                    'label' => 'سطح مسابقات',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارد',
                        'provincial' => 'استانی',
                        'national' => 'کشوری',
                        'international' => 'بین‌المللی',
                    ],
                ],
                [
                    'key' => 'teacher_or_music_institute',
                    'label' => 'استاد یا آموزشگاه',
                    'type' => 'text',
                    'required' => false,
                    'validation' => [
                        'string',
                        'max:255',
                    ],
                ],
            ],
        ],

        [
            'key' => 'technical_skills',
            'title' => 'مهارت‌های فنی',
            'questions' => [
                [
                    'key' => 'mezrab_playing_skill',
                    'label' => 'مضراب‌نوازی',
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
                    'key' => 'fingering_skill',
                    'label' => 'انگشت‌گذاری',
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
                    'key' => 'rhythm_accuracy',
                    'label' => 'دقت ریتم',
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
            ],
        ],
    ],
];

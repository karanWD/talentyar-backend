<?php

return [
    'title' => 'خوانندگی',
    'slug' => 'singing',

    'sections' => [
        [
            'key' => 'voice_characteristics',
            'title' => 'مشخصات صوت',
            'questions' => [
                [
                    'key' => 'voice_type',
                    'label' => 'نوع صدا',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'soprano' => 'سوپرانو',
                        'alto' => 'آلتو',
                        'tenor' => 'تنور',
                        'baritone' => 'باریتون',
                        'bass' => 'باس',
                    ],
                ],
                [
                    'key' => 'voice_power',
                    'label' => 'قدرت صدا',
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
                    'key' => 'vocal_range',
                    'label' => 'وسعت صدا',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                        'professional' => 'حرفه‌ای',
                    ],
                ],
                [
                    'key' => 'breath_control',
                    'label' => 'کنترل تنفس',
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
            'key' => 'singing_information',
            'title' => 'اطلاعات تخصصی خوانندگی',
            'questions' => [
                [
                    'key' => 'specialized_styles',
                    'label' => 'سبک تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'fusion' => 'تلفیقی',
                        'r_and_b' => 'R&B',
                        'pop' => 'پاپ',
                        'traditional' => 'سنتی',
                        'rap' => 'رپ',
                        'rock' => 'راک',
                        'classical' => 'کلاسیک',
                        'jazz' => 'جاز',
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
                    'key' => 'performance_level',
                    'label' => 'سطح اجرا',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'school' => 'مدرسه',
                        'local' => 'محلی',
                        'studio' => 'استودیویی',
                        'concert' => 'کنسرت',
                        'international' => 'بین‌المللی',
                    ],
                ],
            ],
        ],

        [
            'key' => 'additional_skills',
            'title' => 'مهارت‌های جانبی',
            'questions' => [
                [
                    'key' => 'instruments',
                    'label' => 'توانایی نوازندگی',
                    'type' => 'multi_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'piano' => 'پیانو',
                        'guitar' => 'گیتار',
                        'violin' => 'ویولن',
                        'drums' => 'درام',
                        'other' => 'سایر',
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
                    'key' => 'songwriting_skill',
                    'label' => 'توانایی ترانه‌سرایی',
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
            ],
        ],
    ],
];

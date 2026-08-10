<?php

return [
    'title' => 'مجری‌گری',
    'slug' => 'presenting',

    'sections' => [
        [
            'key' => 'personal_and_appearance_information',
            'title' => 'مشخصات فردی و ظاهری',
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
                        'over_180' => 'بیشتر از ۱۸۰ سانتی‌متر',
                    ],
                ],
                [
                    'key' => 'appearance_confidence',
                    'label' => 'اعتماد به نفس ظاهری',
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
                    'key' => 'body_language',
                    'label' => 'زبان بدن',
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
                    'key' => 'eye_contact',
                    'label' => 'ارتباط چشمی',
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
            'key' => 'voice_and_speaking_skills',
            'title' => 'صدا و مهارت‌های گفتاری',
            'questions' => [
                [
                    'key' => 'voice_type',
                    'label' => 'نوع صدا',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'soft' => 'نرم',
                        'formal' => 'رسمی',
                        'powerful' => 'قدرتمند',
                        'energetic' => 'هیجانی',
                        'childlike' => 'کودکانه',
                    ],
                ],
                [
                    'key' => 'improvisation_skill',
                    'label' => 'توانایی بداهه‌گویی',
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
            'key' => 'presenting_information',
            'title' => 'اطلاعات تخصصی مجری‌گری',
            'questions' => [
                [
                    'key' => 'specialized_fields',
                    'label' => 'حوزه تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'television' => 'تلویزیونی',
                        'sports' => 'ورزشی',
                        'children' => 'کودک',
                        'social' => 'اجتماعی',
                        'internet' => 'اینترنتی',
                        'stage' => 'استیج',
                        'radio' => 'رادیویی',
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
                        'interview' => 'مصاحبه',
                        'talk_show' => 'برنامه گفت‌وگومحور',
                        'competition' => 'مسابقه',
                        'event' => 'رویداد',
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
                    'key' => 'event_presenting_experience',
                    'label' => 'تجربه اجرای رویداد',
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

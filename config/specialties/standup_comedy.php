<?php

return [
    'title' => 'استندآپ کمدی',
    'slug' => 'standup_comedy',

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
                        'min:10',
                        'max:100',
                    ],
                ],
                [
                    'key' => 'performance_experience',
                    'label' => 'سابقه اجرا',
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
                    'key' => 'performance_venues',
                    'label' => 'محل‌های اجرا',
                    'type' => 'multi_select',
                    'required' => false,
                    'options' => [
                        'school' => 'مدرسه',
                        'university' => 'دانشگاه',
                        'cafe' => 'کافه',
                        'hall' => 'سالن',
                        'online' => 'فضای مجازی',
                        'television' => 'تلویزیون',
                    ],
                ],
                [
                    'key' => 'live_performances_count',
                    'label' => 'تعداد اجراهای زنده',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        '0_to_10' => '۰ تا ۱۰ اجرا',
                        '10_to_50' => '۱۰ تا ۵۰ اجرا',
                        '50_to_100' => '۵۰ تا ۱۰۰ اجرا',
                        'over_100' => 'بیش از ۱۰۰ اجرا',
                    ],
                ],
            ],
        ],

        [
            'key' => 'performance_skills',
            'title' => 'مهارت‌های اجرا',
            'questions' => [
                [
                    'key' => 'comedy_writing_skill',
                    'label' => 'طنزپردازی',
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
                    'key' => 'storytelling_skill',
                    'label' => 'داستان‌گویی',
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
                    'label' => 'بداهه‌پردازی',
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
                    'key' => 'audience_interaction_skill',
                    'label' => 'ارتباط با مخاطب',
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
                    'key' => 'stage_management_skill',
                    'label' => 'مدیریت صحنه',
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
                    'key' => 'content_creativity_skill',
                    'label' => 'خلاقیت در تولید محتوا',
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
            'key' => 'interests_and_style',
            'title' => 'علایق و سبک اجرا',
            'questions' => [
                [
                    'key' => 'comedy_styles',
                    'label' => 'سبک کمدی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'observational' => 'مشاهده‌ای',
                        'storytelling' => 'داستانی',
                        'social' => 'اجتماعی',
                        'improvisational' => 'بداهه',
                        'character_based' => 'شخصیت‌محور',
                        'combined' => 'ترکیبی',
                    ],
                ],
                [
                    'key' => 'primary_goal',
                    'label' => 'هدف اصلی از فعالیت',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'entertainment' => 'سرگرمی',
                        'content_creation' => 'تولید محتوا',
                        'professional_performance' => 'اجرای حرفه‌ای',
                        'television' => 'فعالیت در تلویزیون',
                    ],
                ],
            ],
        ],
    ],
];

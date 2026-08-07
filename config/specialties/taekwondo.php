<?php

return [
    'title' => 'تکواندو',
    'slug' => 'taekwondo',

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
                    'key' => 'weight_category',
                    'label' => 'رده وزنی',
                    'type' => 'text',
                    'required' => true,
                    'validation' => [
                        'string',
                        'max:100',
                    ],
                ],
                [
                    'key' => 'dominant_foot',
                    'label' => 'پای غالب',
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
            'key' => 'taekwondo_information',
            'title' => 'مشخصات تخصصی تکواندو',
            'questions' => [
                [
                    'key' => 'discipline',
                    'label' => 'رشته تخصصی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'kyorugi' => 'کیوروگی',
                        'poomsae' => 'پومسه',
                        'both' => 'هر دو رشته',
                    ],
                ],
                [
                    'key' => 'activity_level',
                    'label' => 'سطح فعالیت',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'beginner' => 'مبتدی',
                        'academy' => 'آکادمی',
                        'professional' => 'حرفه‌ای',
                        'provincial' => 'استانی',
                        'national' => 'کشوری',
                    ],
                ],
                [
                    'key' => 'belt',
                    'label' => 'کمربند',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'white' => 'سفید',
                        'yellow' => 'زرد',
                        'green' => 'سبز',
                        'blue' => 'آبی',
                        'red' => 'قرمز',
                        'black' => 'مشکی',
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
                [
                    'key' => 'current_club',
                    'label' => 'باشگاه فعلی',
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
                    'key' => 'kicking_skill',
                    'label' => 'ضربات پا',
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
                    'key' => 'technique_execution_speed',
                    'label' => 'سرعت اجرای تکنیک',
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
                    'key' => 'strike_accuracy',
                    'label' => 'دقت ضربات',
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
                    'key' => 'defense_skill',
                    'label' => 'دفاع',
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
                    'key' => 'counterattack_skill',
                    'label' => 'ضدحمله',
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

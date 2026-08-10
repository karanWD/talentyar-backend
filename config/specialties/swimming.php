<?php

return [
    'title' => 'شنا',
    'slug' => 'swimming',

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
                    'key' => 'weight',
                    'label' => 'وزن',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'under_40' => 'کمتر از ۴۰ کیلوگرم',
                        '40_to_50' => '۴۰ تا ۵۰ کیلوگرم',
                        '50_to_60' => '۵۰ تا ۶۰ کیلوگرم',
                        '60_to_70' => '۶۰ تا ۷۰ کیلوگرم',
                        '70_to_80' => '۷۰ تا ۸۰ کیلوگرم',
                        'over_80' => 'بیشتر از ۸۰ کیلوگرم',
                    ],
                ],
                [
                    'key' => 'arm_length',
                    'label' => 'طول دست‌ها',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'short' => 'کوتاه',
                        'medium' => 'متوسط',
                        'long' => 'بلند',
                    ],
                ],
            ],
        ],

        [
            'key' => 'swimming_information',
            'title' => 'مشخصات تخصصی شنا',
            'questions' => [
                [
                    'key' => 'specialized_strokes',
                    'label' => 'رشته تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'freestyle' => 'آزاد',
                        'backstroke' => 'کرال پشت',
                        'breaststroke' => 'قورباغه',
                        'butterfly' => 'پروانه',
                        'individual_medley' => 'مختلط',
                    ],
                ],
                [
                    'key' => 'swimming_level',
                    'label' => 'سطح شنا',
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
                    'key' => 'activity_experience',
                    'label' => 'سابقه فعالیت',
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
    ],
];

<?php

return [
    'title' => 'وزنه‌برداری',
    'slug' => 'weightlifting',

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
                        'under_150' => 'کمتر از ۱۵۰ سانتی‌متر',
                        '150_to_160' => '۱۵۰ تا ۱۶۰ سانتی‌متر',
                        '160_to_170' => '۱۶۰ تا ۱۷۰ سانتی‌متر',
                        '170_to_180' => '۱۷۰ تا ۱۸۰ سانتی‌متر',
                        '180_to_190' => '۱۸۰ تا ۱۹۰ سانتی‌متر',
                        'over_190' => 'بیشتر از ۱۹۰ سانتی‌متر',
                    ],
                ],
                [
                    'key' => 'weight_category',
                    'label' => 'دسته وزنی',
                    'type' => 'text',
                    'required' => true,
                    'validation' => [
                        'string',
                        'max:100',
                    ],
                ],
                [
                    'key' => 'arm_length',
                    'label' => 'طول دست',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'short' => 'کوتاه',
                        'medium' => 'متوسط',
                        'long' => 'بلند',
                    ],
                ],
                [
                    'key' => 'leg_length',
                    'label' => 'طول پا',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'short' => 'کوتاه',
                        'medium' => 'متوسط',
                        'long' => 'بلند',
                    ],
                ],
                [
                    'key' => 'snatch_record',
                    'label' => 'رکورد یک‌ضرب',
                    'type' => 'number',
                    'required' => false,
                    'validation' => [
                        'numeric',
                        'min:0',
                        'max:300',
                    ],
                ],
                [
                    'key' => 'clean_and_jerk_record',
                    'label' => 'رکورد دوضرب',
                    'type' => 'number',
                    'required' => false,
                    'validation' => [
                        'numeric',
                        'min:0',
                        'max:400',
                    ],
                ],
            ],
        ],

        [
            'key' => 'weightlifting_information',
            'title' => 'مشخصات تخصصی وزنه‌برداری',
            'questions' => [
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

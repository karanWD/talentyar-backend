<?php

return [
    'title' => 'فوتبال',
    'slug' => 'football',

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
                        '180_to_190' => '۱۸۰ تا ۱۹۰ سانتی‌متر',
                        'over_190' => 'بیشتر از ۱۹۰ سانتی‌متر',
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
                        '80_to_90' => '۸۰ تا ۹۰ کیلوگرم',
                        'over_90' => 'بیشتر از ۹۰ کیلوگرم',
                    ],
                ],
                [
                    'key' => 'dominant_foot',
                    'label' => 'پای تخصصی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'right' => 'راست',
                        'left' => 'چپ',
                        'both' => 'هر دو پا',
                    ],
                ],
            ],
        ],

        [
            'key' => 'football_information',
            'title' => 'مشخصات تخصصی فوتبال',
            'questions' => [
                [
                    'key' => 'playing_position',
                    'label' => 'پست تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'goalkeeper' => 'دروازه‌بان',
                        'center_back' => 'مدافع میانی',
                        'full_back' => 'مدافع کناری',
                        'defensive_midfielder' => 'هافبک دفاعی',
                        'central_midfielder' => 'هافبک میانی',
                        'attacking_midfielder' => 'هافبک هجومی',
                        'winger' => 'وینگر',
                        'striker' => 'مهاجم',
                    ],
                ],
                [
                    'key' => 'playing_level',
                    'label' => 'سطح فعالیت',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'beginner' => 'مبتدی',
                        'club' => 'باشگاهی',
                        'academy' => 'آکادمی',
                        'professional' => 'حرفه‌ای',
                        'provincial' => 'استانی',
                        'national' => 'ملی',
                    ],
                ],
                [
                    'key' => 'team_or_academy_name',
                    'label' => 'اسم تیم یا آکادمی',
                    'type' => 'text',
                    'required' => false,
                    'validation' => [
                        'string',
                        'max:255',
                    ],
                ],
                [
                    'key' => 'favorite_domestic_team',
                    'label' => 'تیم مورد علاقه داخلی',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'persepolis' => 'پرسپولیس',
                        'esteghlal' => 'استقلال',
                        'tractor' => 'تراکتور',
                        'sepahan' => 'سپاهان',
                        'other' => 'سایر تیم‌ها',
                    ],
                ],
            ],
        ],

        [
            'key' => 'competition_experience',
            'title' => 'تجربه مسابقات',
            'questions' => [
                [
                    'key' => 'official_match_experience',
                    'label' => 'تجربه مسابقه رسمی',
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
                    'key' => 'official_matches_count',
                    'label' => 'تعداد مسابقات رسمی',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'up_to_10' => 'تا ۱۰ مسابقه',
                        '10_to_30' => '۱۰ تا ۳۰ مسابقه',
                        '30_to_70' => '۳۰ تا ۷۰ مسابقه',
                        'over_70' => 'بیش از ۷۰ مسابقه',
                    ],
                ],
                [
                    'key' => 'highest_competition_level',
                    'label' => 'بالاترین سطح مسابقه',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'school_local' => 'مدرسه / محلی',
                        'club' => 'باشگاهی',
                        'provincial' => 'استانی',
                        'national' => 'کشوری',
                        'international' => 'بین‌المللی',
                    ],
                ],
                [
                    'key' => 'national_team_experience',
                    'label' => 'سابقه حضور در تیم منتخب',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'none' => 'ندارم',
                        'regional_city' => 'منطقه / شهر',
                        'provincial' => 'استان',
                        'country' => 'کشوری',
                        'national_team' => 'ملی',
                    ],
                ],
            ],
        ],

        [
            'key' => 'achievements',
            'title' => 'تجربه و افتخارات',
            'questions' => [
                [
                    'key' => 'achievement_level',
                    'label' => 'سطح افتخارات',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'none' => 'ندارم',
                        'club_local' => 'باشگاهی / محلی',
                        'provincial' => 'استانی',
                        'national' => 'کشوری',
                        'international' => 'بین‌المللی',
                    ],
                ],
                [
                    'key' => 'most_important_achievement',
                    'label' => 'مهم‌ترین افتخار',
                    'type' => 'text',
                    'required' => false,
                    'validation' => [
                        'string',
                        'max:500',
                    ],
                ],
            ],
        ],
    ],
];

<?php

return [
    'title' => 'بازیگری در پلتفرم‌های اجتماعی',
    'slug' => 'social_media_acting',

    'sections' => [
        [
            'key' => 'appearance_information',
            'title' => 'مشخصات ظاهری',
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
                    'key' => 'eye_color',
                    'label' => 'رنگ چشم',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'brown' => 'قهوه‌ای',
                        'black' => 'مشکی',
                        'blue' => 'آبی',
                        'green' => 'سبز',
                        'hazel' => 'عسلی',
                        'gray' => 'خاکستری',
                    ],
                ],
                [
                    'key' => 'hair_color',
                    'label' => 'رنگ مو',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'black' => 'مشکی',
                        'brown' => 'قهوه‌ای',
                        'blonde' => 'بلوند',
                        'red' => 'قرمز',
                        'gray' => 'خاکستری',
                        'white' => 'سفید',
                    ],
                ],
                [
                    'key' => 'hair_types',
                    'label' => 'نوع مو',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'straight' => 'صاف',
                        'wavy' => 'موج‌دار',
                        'curly' => 'فر',
                        'short' => 'کوتاه',
                        'long' => 'بلند',
                    ],
                ],
                [
                    'key' => 'skin_tone',
                    'label' => 'رنگ پوست',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'fair' => 'روشن',
                        'wheat' => 'گندمی',
                        'olive' => 'سبزه',
                        'dark' => 'تیره',
                    ],
                ],
            ],
        ],

        [
            'key' => 'acting_experience',
            'title' => 'تجربه بازیگری',
            'questions' => [
                [
                    'key' => 'acting_experience',
                    'label' => 'سابقه بازیگری داری؟',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'no_experience' => 'ندارم',
                        'under_1_year' => 'کمتر از ۱ سال',
                        '1_to_3_years' => '۱ تا ۳ سال',
                        'over_3_years' => 'بیش از ۳ سال',
                    ],
                ],
            ],
        ],

        [
            'key' => 'voice_and_dialect',
            'title' => 'صدا و گویش',
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
                        'baluchi' => 'بلوچی',
                        'other' => 'سایر',
                    ],
                ],
                [
                    'key' => 'performable_accents',
                    'label' => 'لهجه‌های قابل اجرا',
                    'type' => 'multi_select',
                    'required' => false,
                    'options' => [
                        'tehrani' => 'تهرانی',
                        'southern' => 'جنوبی',
                        'turkish' => 'ترکی',
                        'kurdish' => 'کردی',
                        'northern' => 'شمالی',
                        'isfahani' => 'اصفهانی',
                        'mashhadi' => 'مشهدی',
                        'sh irazi' => 'شیرازی',
                    ],
                ],
                [
                    'key' => 'voice_type',
                    'label' => 'نوع صدا',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'childlike' => 'کودکانه',
                        'soft' => 'نرم',
                        'normal' => 'معمولی',
                        'deep' => 'بم',
                        'powerful' => 'قدرتمند',
                    ],
                ],
                [
                    'key' => 'spoken_languages',
                    'label' => 'زبان‌های قابل صحبت',
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
            'key' => 'social_media_performance',
            'title' => 'اجرای ویژه شبکه‌های اجتماعی',
            'questions' => [
                [
                    'key' => 'camera_comfort_level',
                    'label' => 'جلوی دوربین چقدر راحت هستی؟',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'very_low' => 'خیلی کم',
                        'medium' => 'متوسط',
                        'comfortable' => 'راحت',
                        'fully_confident' => 'کاملاً مسلط',
                    ],
                ],
                [
                    'key' => 'reels_or_shorts_experience',
                    'label' => 'تجربه ساخت Reels یا Shorts داری؟',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'none' => 'خیر',
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                    ],
                ],
                [
                    'key' => 'advertising_performance_experience',
                    'label' => 'تجربه اجرای تبلیغاتی داری؟',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        'none' => 'خیر',
                        'once_or_twice' => 'یک یا دو بار',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                    ],
                ],
            ],
        ],
    ],
];

<?php

return [
    'title' => 'کارگردانی',
    'slug' => 'directing',

    'sections' => [
        [
            'key' => 'directing_information',
            'title' => 'اطلاعات تخصصی کارگردانی',
            'questions' => [
                [
                    'key' => 'specialized_fields',
                    'label' => 'حوزه تخصصی',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'short_film' => 'فیلم کوتاه',
                        'cinema' => 'سینما',
                        'series' => 'سریال',
                        'documentary' => 'مستند',
                        'advertising' => 'تبلیغات',
                        'music_video' => 'موزیک‌ویدیو',
                        'theater' => 'تئاتر',
                    ],
                ],
                [
                    'key' => 'preferred_genres',
                    'label' => 'سبک مورد علاقه',
                    'type' => 'multi_select',
                    'required' => true,
                    'options' => [
                        'drama' => 'درام',
                        'action' => 'اکشن',
                        'comedy' => 'کمدی',
                        'horror' => 'ترسناک',
                        'social' => 'اجتماعی',
                        'artistic' => 'هنری',
                        'science_fiction' => 'علمی‌تخیلی',
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
                        'studio' => 'سطح استودیویی',
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
                    'key' => 'completed_projects_count',
                    'label' => 'تعداد پروژه‌های انجام‌شده',
                    'type' => 'single_select',
                    'required' => true,
                    'options' => [
                        '0_to_5' => 'تا ۵ پروژه',
                        '5_to_10' => '۵ تا ۱۰ پروژه',
                        '10_to_20' => '۱۰ تا ۲۰ پروژه',
                        'over_20' => 'بیشتر از ۲۰ پروژه',
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
                        'university' => 'دانشگاه',
                        'professional_academy' => 'آکادمی حرفه‌ای',
                    ],
                ],
            ],
        ],

        [
            'key' => 'creative_and_narrative_skills',
            'title' => 'مهارت‌های خلاقیت و روایت',
            'questions' => [
                [
                    'key' => 'storytelling_skill',
                    'label' => 'قدرت داستان‌گویی',
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
                    'key' => 'visual_creativity',
                    'label' => 'خلاقیت بصری',
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
                    'key' => 'atmosphere_creation_skill',
                    'label' => 'قدرت فضاسازی',
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
                    'key' => 'scene_design_skill',
                    'label' => 'طراحی سکانس',
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
                    'key' => 'idea_development_skill',
                    'label' => 'ایده‌پردازی',
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
                    'key' => 'film_rhythm_understanding',
                    'label' => 'درک ریتم فیلم',
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
            'key' => 'experience_and_achievements',
            'title' => 'تجربه و افتخارات',
            'questions' => [
                [
                    'key' => 'festival_experience',
                    'label' => 'تجربه جشنواره',
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
                    'key' => 'most_important_achievement',
                    'label' => 'مهم‌ترین افتخار',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'local_festival' => 'جشنواره محلی',
                        'national_festival' => 'جشنواره کشوری',
                        'official_screening' => 'اکران رسمی',
                        'international' => 'افتخار بین‌المللی',
                    ],
                ],
                [
                    'key' => 'professional_collaboration_experience',
                    'label' => 'تجربه همکاری حرفه‌ای',
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
                    'key' => 'professional_actors_experience',
                    'label' => 'تجربه کار با بازیگران حرفه‌ای',
                    'type' => 'single_select',
                    'required' => false,
                    'options' => [
                        'none' => 'ندارم',
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                    ],
                ],
            ],
        ],
    ],
];

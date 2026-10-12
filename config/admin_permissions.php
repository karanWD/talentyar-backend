<?php

return [
    'guard' => 'api-admin',

    /*
    |--------------------------------------------------------------------------
    | Admin permission catalogue
    |--------------------------------------------------------------------------
    |
    | Existing action permission names are intentionally kept stable because
    | they may already be assigned to production roles. Field permissions are
    | additive and only apply to authenticated administrators.
    |
    */
    'modules' => [
        'users' => [
            'label' => 'کاربران',
            'actions' => [
                'create' => ['name' => 'create-users', 'label' => 'ایجاد کاربر'],
                'view' => ['name' => 'read-users', 'label' => 'مشاهده کاربران'],
                'update' => ['name' => 'update-users', 'label' => 'ویرایش کاربر'],
                'delete' => ['name' => 'delete-users', 'label' => 'حذف کاربر'],
            ],
            'fields' => [
                'phone' => [
                    'label' => 'شماره تماس',
                    'permissions' => [
                        'view' => ['name' => 'users.fields.phone.view', 'label' => 'مشاهده شماره تماس'],
                        'update' => [
                            'name' => 'users.fields.phone.update',
                            'label' => 'ویرایش شماره تماس',
                            'requires' => ['users.fields.phone.view'],
                        ],
                    ],
                ],
                'email' => [
                    'label' => 'ایمیل',
                    'permissions' => [
                        'view' => ['name' => 'users.fields.email.view', 'label' => 'مشاهده ایمیل'],
                        'update' => [
                            'name' => 'users.fields.email.update',
                            'label' => 'ویرایش ایمیل',
                            'requires' => ['users.fields.email.view'],
                        ],
                    ],
                ],
                'birth_date' => [
                    'label' => 'تاریخ تولد',
                    'permissions' => [
                        'view' => ['name' => 'users.fields.birth_date.view', 'label' => 'مشاهده تاریخ تولد'],
                        'update' => [
                            'name' => 'users.fields.birth_date.update',
                            'label' => 'ویرایش تاریخ تولد',
                            'requires' => ['users.fields.birth_date.view'],
                        ],
                    ],
                ],
                'subscription' => [
                    'label' => 'اشتراک',
                    'permissions' => [
                        'view' => ['name' => 'users.fields.subscription.view', 'label' => 'مشاهده اشتراک'],
                    ],
                ],
                'referral_code' => [
                    'label' => 'کد معرف',
                    'permissions' => [
                        'view' => ['name' => 'users.fields.referral_code.view', 'label' => 'مشاهده کد معرف'],
                    ],
                ],
            ],
        ],
        'admins' => [
            'label' => 'مدیران',
            'actions' => [
                'create' => ['name' => 'create-admins', 'label' => 'ایجاد مدیر'],
                'view' => ['name' => 'read-admins', 'label' => 'مشاهده مدیران'],
                'update' => ['name' => 'update-admins', 'label' => 'ویرایش مدیر'],
                'delete' => ['name' => 'delete-admins', 'label' => 'حذف مدیر'],
            ],
        ],
        'roles' => [
            'label' => 'نقش‌ها',
            'actions' => [
                'create' => ['name' => 'create-roles', 'label' => 'ایجاد نقش'],
                'view' => ['name' => 'read-roles', 'label' => 'مشاهده نقش‌ها'],
                'update' => ['name' => 'update-roles', 'label' => 'ویرایش نقش'],
                'delete' => ['name' => 'delete-roles', 'label' => 'حذف نقش'],
            ],
        ],
        'permissions' => [
            'label' => 'دسترسی‌ها',
            'actions' => [
                'view' => ['name' => 'read-permissions', 'label' => 'مشاهده دسترسی‌ها'],
            ],
        ],
        'posts' => [
            'label' => 'پست‌ها',
            'actions' => [
                'view' => ['name' => 'read-posts', 'label' => 'مشاهده پست‌ها'],
                'update' => ['name' => 'update-posts', 'label' => 'ویرایش پست'],
                'delete' => ['name' => 'delete-posts', 'label' => 'حذف پست'],
            ],
        ],
        'evaluations' => [
            'label' => 'ارزیابی‌ها',
            'actions' => [
                'view' => ['name' => 'read-evaluation', 'label' => 'مشاهده ارزیابی‌ها'],
                'update' => ['name' => 'update-evaluation', 'label' => 'ویرایش ارزیابی'],
                'delete' => ['name' => 'delete-evaluation', 'label' => 'حذف ارزیابی'],
            ],
        ],
        'items' => [
            'label' => 'آیتم‌ها',
            'actions' => [
                'view' => ['name' => 'read-items', 'label' => 'مشاهده آیتم‌ها'],
            ],
        ],
    ],
];

<?php

namespace App\Traits;

trait PermissionTranslationTrait
{
    public function translatePermission($permissionName)
    {
        $translations = [
            // دسته‌بندی‌ها
            'create-categories' => 'ایجاد دسته‌بندی',
            'read-categories' => 'مشاهده دسته‌بندی',
            'update-categories' => 'ویرایش دسته‌بندی',
            'delete-categories' => 'حذف دسته‌بندی',

            // ادمین‌ها
            'create-admins' => 'ایجاد ادمین',
            'read-admins' => 'مشاهده ادمین',
            'update-admins' => 'ویرایش ادمین',
            'delete-admins' => 'حذف ادمین',

            // نقش‌ها
            'create-roles' => 'ایجاد نقش',
            'read-roles' => 'مشاهده نقش',
            'update-roles' => 'ویرایش نقش',
            'delete-roles' => 'حذف نقش',

            // محصول‌ها
            'create-product' => 'ایجاد محصول',
            'read-product' => 'مشاهده محصول',
            'update-product' => 'ویرایش محصول',
            'delete-product' => 'حذف محصول',

            // قیمت محصول
            'create-product-price' => 'ایجاد قیمت محصول',
            'read-product-price' => 'مشاهده قیمت محصول',
            'update-product-price' => 'ویرایش قیمت محصول',
            'delete-product-price' => 'حذف قیمت محصول',

            // انبار
            'read-warehouse' => 'مشاهده انبار',
            'update-warehouse-in' => 'ورود به انبار',
            'update-warehouse-out' => 'خروج از انبار',
        ];

        return $translations[$permissionName] ?? $permissionName;
    }
}


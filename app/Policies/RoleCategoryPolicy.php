<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Product;

class RoleCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewProduct(Admin $admin, Product $product): bool
    {
        return $admin->roles()->whereHas('categories', function ($q) use ($product) {
            $q->where('category_id', $product->category_id)
                ->where('permission_level', 'price_edit');
        })->exists();    }
}

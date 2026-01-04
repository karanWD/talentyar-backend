<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Livewire\Admins\Admin\Index as AdminsIndex;
use App\Livewire\Admins\Category\Index as CategoryIndex;
use App\Livewire\Admins\Roles\Index as RolesIndex;
use Illuminate\Support\Facades\Route;




Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profile', [AdminAuthController::class, 'show'])->name('profile');
    Route::post('/profile', [AdminAuthController::class, 'update'])->name('profile.update');
});

Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');


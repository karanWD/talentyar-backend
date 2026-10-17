<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\EvaluationsController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
        });
    });

    Route::middleware('auth:api-admin')->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::prefix('auth')->name('auth.')->group(function () {
                Route::get('/profile', [AdminAuthController::class, 'profile'])->name('profile');
                Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
            });

            Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
            Route::post('/roles', [AdminRoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}', [AdminRoleController::class, 'show'])->name('roles.show');
            Route::put('/roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('roles.destroy');
            Route::get('/permissions', [AdminPermissionController::class, 'index'])->name('permissions.index');

            Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
            Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
            Route::get('/admins/{admin}', [AdminController::class, 'show'])->name('admins.show');
            Route::put('/admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
            Route::patch('/admins/{admin}', [AdminController::class, 'update'])->name('admins.patch');
            Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])->name('admins.destroy');

            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/referral-codes', [UserController::class, 'referralCodes'])->name('users.referral-codes');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
            Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
            Route::post('/posts/{post}/state', [PostController::class, 'updateState'])->name('posts.state');
            Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

            Route::get('/evaluations', [EvaluationsController::class, 'index'])->name('evaluations.index');
            Route::get('/evaluations/{evaluation}', [EvaluationsController::class, 'show'])->name('evaluations.show');
            Route::post('/evaluations/{evaluation}/store', [EvaluationsController::class, 'store'])->name('evaluations.store');
            Route::post('/evaluations/{evaluation}/status', [EvaluationsController::class, 'updateStatus'])->name('evaluations.status');
            Route::post('/evaluations/{evaluation}/details', [EvaluationsController::class, 'updateDetails'])->name('evaluations.details');
            Route::post('/evaluations/{evaluation}/retry', [EvaluationsController::class, 'retry'])->name('evaluations.retry');
            Route::delete('/evaluations/{evaluation}', [EvaluationsController::class, 'destroy'])->name('evaluations.destroy');

            Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        });
    });
});

// Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
//    Route::get('/profile', [AdminAuthController::class, 'show'])->name('profile');
//    Route::post('/profile', [AdminAuthController::class, 'update'])->name('profile.update');
// });
//
// Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
// Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
// Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

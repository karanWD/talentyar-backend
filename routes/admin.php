<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminRoleController;
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


            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
            

            Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
            Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
            Route::post('/posts/{post}/state', [PostController::class, 'updateState'])->name('posts.state');
        });
    });
});


//Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
//    Route::get('/profile', [AdminAuthController::class, 'show'])->name('profile');
//    Route::post('/profile', [AdminAuthController::class, 'update'])->name('profile.update');
//});
//
//Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
//Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
//Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');


<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Employer\AuthController as EmployerAuthController;
use App\Http\Controllers\Employer\CompanyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->name('v1.')->group(function () {
    // Public routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/otp', [AuthController::class, 'requestOtp'])->name('otp.request');
            Route::post('/login', [AuthController::class, 'verifyOtp'])->name('otp.verify');
        });
    });


    Route::prefix('employer')->name('employer.')->group(function () {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/otp', [EmployerAuthController::class, 'requestOtp'])->name('otp.request');
            Route::post('/login', [EmployerAuthController::class, 'verifyOtp'])->name('otp.verify');
        });
    });


    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::prefix('auth')->name('auth.')->group(function () {
                Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
                Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout.all');
            });
            Route::get('/profile', [AuthController::class, 'getProfile'])->name('get.profile');
            Route::post('/profile', [AuthController::class, 'updateProfile'])->name('update.profile');
        });

        Route::prefix('employer')->name('employer.')->group(function () {
            Route::prefix('auth')->name('auth.')->group(function () {
                Route::post('/logout', [EmployerAuthController::class, 'logout'])->name('logout');
                Route::post('/logout-all', [EmployerAuthController::class, 'logoutAll'])->name('logout.all');
            });
            Route::get('/profile', [EmployerAuthController::class, 'getProfile'])->name('get.profile');
            Route::post('/profile', [EmployerAuthController::class, 'updateProfile'])->name('update.profile');

            Route::post('/media/upload', [EmployerAuthController::class, 'uploadMedia'])->name('media.upload');

            // Company routes
            Route::prefix('company')->name('company.')->group(function () {
                // Route to get the authenticated employer's company info
                Route::get('/', [CompanyController::class, 'show'])->name('show');
                // Route to store or update the employer's company
                Route::post('/', [CompanyController::class, 'storeOrUpdate'])->name('storeOrUpdate');
            });
        });
    });
});

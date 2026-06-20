<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Service\EvaluationsController as ServiceEvaluationsController;
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
require __DIR__ . '/admin.php';

Route::prefix('v1')->name('v1.')->group(function () {
//    Route::prefix('admin')->name('admin.')->group(function () {
//        Route::prefix('auth')->name('auth.')->group(function () {
//            Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
//        });
//    });

    // Public routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/otp', [AuthController::class, 'requestOtp'])->name('otp.request');
            Route::post('/login', [AuthController::class, 'verifyOtp'])->name('otp.verify');
        });
    });


    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::prefix('auth')->name('auth.')->group(function () {
                Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
                Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout.all');
                Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
            });
            Route::get('/profile', [AuthController::class, 'getProfile'])->name('get.profile');
            Route::post('/profile', [AuthController::class, 'updateProfile'])->name('update.profile');
            Route::post('/profile/avatar', [AuthController::class, 'updateProfileAvatar'])->name('update.profile.avatar');


            Route::post('/username/check', [AuthController::class, 'checkUsername'])
                ->middleware('throttle:20,1')
                ->name('username.check');

            Route::get('/followers', [FollowController::class, 'followers'])->name('followers');
            Route::get('/following', [FollowController::class, 'following'])->name('following');

            Route::get('/following/{username}', [FollowController::class, 'followingUsername'])->name('following.username');
            Route::get('/followers/{username}', [FollowController::class, 'followersUsername'])->name('followers.username');

            Route::post('/follow/{user}', [FollowController::class, 'follow'])->name('follow');
            Route::delete('/follow/{user}', [FollowController::class, 'unfollow'])->name('unfollow');

            Route::post('/media', [MediaController::class, 'upload'])->name('media.upload');
            Route::get('/posts', [PostController::class, 'myPosts'])->name('posts.index');
            Route::get('/{username}/posts', [PostController::class, 'userPosts'])->name('userPosts.index');
            Route::get('/feed', [PostController::class, 'feed'])->name('feed');
            Route::get('/explore', [PostController::class, 'explore'])->name('feed');

            Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

            Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
            Route::post('/posts/{post}/dislike', [PostController::class, 'dislike'])->name('posts.dislike');
            Route::delete('/posts/{post}/reaction', [PostController::class, 'removeReaction'])->name('posts.reaction.remove');

            Route::get('/posts/{post}/comments', [PostController::class, 'comments'])->name('posts.comments.index');
            Route::post('/posts/{post}/comments', [PostController::class, 'storeComment'])->name('posts.comments.store');
            Route::post('/posts/{post}/view', [PostController::class, 'recordView'])->name('posts.view');

            Route::get('/users/search', [UserController::class, 'search'])->name('users.search');

            Route::get('/evaluations/months', [EvaluationController::class, 'months'])->name('evaluations.months');
            Route::post('/evaluations/media', [EvaluationController::class, 'upload'])->name('evaluations.upload');
            Route::post('/evaluations/store', [EvaluationController::class, 'store'])->name('evaluations.store');

            Route::get('/users/{username}', [UserController::class, 'showByUsername'])->name('users.show');

            Route::post('/subscription/purchase', [SubscriptionController::class, 'purchase'])->name('subscription.purchase');
            Route::get('/subscription', [SubscriptionController::class, 'getActiveSubscription'])->name('subscription.active');
        });
    });


    Route::prefix('public')->name('public.')->group(function () {
        Route::get('/provinces', [PublicController::class, 'getProvinces'])->name('provinces');
        Route::get('/cities', [PublicController::class, 'getCities'])->name('cities');
        Route::get('/plans', [PlanController::class, 'index'])->name('plans');
    });

    Route::middleware('service.token')->prefix('service')->name('service.')->group(function () {
        Route::get('/evaluations', [ServiceEvaluationsController::class, 'index'])->name('evaluations.index');
        Route::get('/evaluations/{evaluation}', [ServiceEvaluationsController::class, 'show'])->name('evaluations.show');
        Route::get('/evaluations/{evaluation}/analysis-status', [ServiceEvaluationsController::class, 'analysisStatus'])->name('evaluations.analysis-status');
        Route::post('/evaluations/{evaluation}/store', [ServiceEvaluationsController::class, 'store'])->name('evaluations.store');
        Route::post('/evaluations/{evaluation}/fail', [ServiceEvaluationsController::class, 'fail'])->name('evaluations.fail');
    });

    Route::get('/callback', [SubscriptionController::class, 'callback'])->name('callback');

});

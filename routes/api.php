<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\UserSiteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SiteController;

// 認証関連
Route::middleware(['throttle:login'])->post('/login', [AuthController::class, 'login']);
Route::middleware(['throttle:register'])->post('/register', RegisterController::class);
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
Route::middleware(['throttle:email-resend'])->post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.send');
Route::middleware(['throttle:reset-password-send'])->post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.reset');

// 認証不要
Route::get('/search', [SearchController::class, 'viewAny']);
Route::get('/site/{site}/thumbnail', [SiteController::class, 'thumbnail']);

// 認証付きルート（セッションベース）
Route::middleware(['auth:sanctum'])->group(function () {
    // 認証関連
    Route::post('/logout', [AuthController::class, 'logout']);

    // その他
    Route::get('/user', [UserController::class, 'show']);
    Route::post('/user/change-password', [UserController::class, 'changePassword']);
    Route::get('/user/sites', [UserSiteController::class, 'viewAny']);
    Route::post('/user/sites', [UserSiteController::class, 'create']);
    Route::put('/user/sites/{site}', [UserSiteController::class, 'update']);
    Route::delete('/user/sites/{site}', [UserSiteController::class, 'delete']);
});

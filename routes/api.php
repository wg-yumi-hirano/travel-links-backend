<?php declare(strict_types=1);

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\UserSiteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SiteController;
use App\Models\User;

// 認証関連
Route::middleware(['throttle:login'])->post('/login', [AuthController::class, 'login']);
Route::middleware(['throttle:register'])->post('/register', RegisterController::class);
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
Route::middleware(['throttle:email-resend'])->post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.send');

// 認証不要
Route::get('/search', [SearchController::class, 'viewAny']);
Route::get('/site/{site}/thumbnail', [SiteController::class, 'thumbnail']);

// 認証付きルート（セッションベース）
Route::middleware(['auth:sanctum'])->group(function () {
    // 認証関連
    Route::post('/logout', [AuthController::class, 'logout']);

    // その他
    Route::get('/user', [UserController::class, 'show']);
    Route::get('/user/sites', [UserSiteController::class, 'viewAny']);
    Route::post('/user/sites', [UserSiteController::class, 'create']);
    Route::put('/user/sites/{site}', [UserSiteController::class, 'update']);
    Route::delete('/user/sites/{site}', [UserSiteController::class, 'delete']);
});

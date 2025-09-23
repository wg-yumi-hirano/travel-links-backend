<?php declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SiteController;
use App\Models\User;

// 認証関連
Route::middleware(['throttle:login'])->post('/login', [AuthController::class, 'login']);
Route::middleware(['throttle:register'])->post('/register', RegisterController::class);

// 認証不要
Route::get('/search', [SearchController::class, 'index']);
Route::get('/site/{site}/thumbnail', [SiteController::class, 'thumbnail']);

// 認証付きルート（セッションベース）
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'show']);
    // // 他の保護されたAPI
    // Route::get('/dashboard', [UserController::class, 'dashboard']);
});


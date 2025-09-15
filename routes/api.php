<?php declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SearchController;
use App\Models\User;

/* 認証関連 */

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', RegisterController::class);

/* その他 */

Route::get('/search', [SearchController::class, 'index']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

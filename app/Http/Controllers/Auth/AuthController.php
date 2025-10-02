<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;

class AuthController extends \App\Http\Controllers\Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $limit = Config::get('project.login_throttle_limit', 5);
        $decay = Config::get('project.login_throttle_decay_second', 10);
        $key = $this->generateRateLimitKey($request);
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            Log::warning('Too many attempts to login', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->error(
                Lang::get('project.auth_too_many_attempts', ['seconds' => $decay]),
                null,
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        RateLimiter::hit($key, $decay);
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return $this->error(
                __('project.auth_failed'),
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return $this->success(Auth::user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();  // トークン削除

        Auth::guard('api')->logout();

        $request->session()->invalidate();  // セッションIDを無効化
        $request->session()->regenerateToken();  // CSRFトークンを再生成

        return $this->success();
    }

    private function generateRateLimitKey(LoginRequest $request)
    {
        return Str::lower($request->input('email')). '|' . $request->ip();
    }
}

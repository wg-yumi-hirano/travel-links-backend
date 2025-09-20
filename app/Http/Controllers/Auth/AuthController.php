<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

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
    public function login(LoginRequest $request)
    {
        $limit = Config::get('project.login_throttle_limit', 5);
        $decay = Config::get('project.login_throttle_decay_second', 10);
        $key = 'login:' . Str::lower($request->input('login_id')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return $this->error(
                Lang::get('project.auth_too_many_attempts', ['seconds' => $decay]),
                null,
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        RateLimiter::hit($key, $decay);
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            Log::warning('Login failed', [
                'login_id' => $credentials['login_id'] ?? null,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $user = User::where('login_id', $credentials['login_id'])->first();
            if ($user) {
                $user->increment('login_failed_count');
            }

            return $this->error(
                __('project.auth_failed'),
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return $this->success(__('project.auth_login_success'), Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();         // セッションIDを無効化
        $request->session()->regenerateToken();    // CSRFトークンを再生成

        return $this->success(__('project.auth_logout_success'));
    }
}

<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    public function configureRateLimiting(): void
    {
        // ログイン試行制限
        RateLimiter::for('login', function (Request $request) {
            $key = 'login:' . Str::lower($request->input('email')) . '|' . $request->ip();

            return Limit::perMinute(config('project.login.throttle_limit', 5))
                        ->by($key);
        });

        // ユーザー登録試行制限
        RateLimiter::for('register', function (Request $request) {
            $key = 'register:' . Str::lower($request->input('email')) . '|' . $request->ip();

            return Limit::perMinute(config('project.register.throttle_limit', 3))
                        ->by($key);
        });

        // メール確認試行制限
        RateLimiter::for('email-resend', function (Request $request) {
            $key = 'verification:' . Str::lower($request->input('email')) . '|' . $request->ip();

            return Limit::perMinutes(
                config('project.verification.throttle_decay_minute', 1),
                config('project.verification.throttle_limit', 6)
            )->by($key);
        });
    }
}
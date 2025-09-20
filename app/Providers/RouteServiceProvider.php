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
            $key = 'login:' . Str::lower($request->input('login_id')) . '|' . $request->ip();

            return Limit::perMinute(config('project.login_throttle_limit', 5))
                        ->by($key);
        });

        // ユーザー登録試行制限
        RateLimiter::for('register', function (Request $request) {
            $key = 'login:' . Str::lower($request->input('login_id')) . '|' . $request->ip();

            return Limit::perMinute(config('project.register_throttle_limit', 3))
                        ->by($key);
        });
    }
}
<?php declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\ExceptionConfigurator;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // See: https://readouble.com/laravel/12.x/ja/csrf.html
        $middleware->validateCsrfTokens(except: [
            // 認証済みを前提とした API は除外
            'api/user',
            'api/user/*'
        ]);

        // See: https://laravel.com/docs/12.x/middleware
        $m = [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ];
        $middleware->group('api', $m);
        $middleware->priority($m);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        ExceptionConfigurator::configure($exceptions);
    })->create();

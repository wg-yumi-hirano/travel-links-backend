<?php declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Gate::policy(\App\Models\Site::class, \App\Policies\UserSitePolicy::class);
    }

    public function boot(): void
    {
        if (App::environment('local')) {
            DB::listen(function ($query) {
                Log::info('SQL Info', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time_ms' => $query->time,
                ]);
            });
        }
    }
}

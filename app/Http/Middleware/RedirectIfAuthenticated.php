<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect('/home'); // SPAなら `/` や `/dashboard` に変更可能
            }
        }

        return $next($request);
    }
}
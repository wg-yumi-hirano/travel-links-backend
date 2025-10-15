<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogRequestInfo
{
    protected array $filteredKeys = ['password', 'password_confirmation', 'current_password'];
    protected array $truncatedKeys = ['thumbnail', 'description'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        foreach ($this->filteredKeys as $key) {
            if (array_key_exists($key, $input)) {
                $input[$key] = '[FILTERED]';
            }
        }
        foreach ($this->truncatedKeys as $key) {
            if (array_key_exists($key, $input)) {
                $input[$key] = Str::limit($input[$key], 50, '...');
            }
        }

        Log::info('Request Info', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'input' => $input,
        ]);

        return $next($request);
    }
}

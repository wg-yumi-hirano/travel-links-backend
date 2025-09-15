<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * 認証されていない場合のリダイレクト先
     */
    protected function redirectTo($request): ?string
    {
        return null;
    }
}
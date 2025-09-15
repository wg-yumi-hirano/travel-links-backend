<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    // 必要に応じて $except 配列で除外ルートを指定できます
    protected $except = [];
}
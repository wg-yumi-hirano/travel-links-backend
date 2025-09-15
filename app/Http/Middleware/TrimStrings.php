<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * トリムしない属性のリスト
     *
     * @var array
     */
    protected $except = [
        // 例: 'password', 'password_confirmation'
    ];
}
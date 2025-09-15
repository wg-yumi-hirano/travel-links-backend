<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * 信頼するプロキシのリスト
     *
     * @var array|string|null
     */
    protected $proxies;

    /**
     * ヘッダーの種類（X-Forwarded-For など）
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
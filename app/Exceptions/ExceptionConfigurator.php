<?php declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Helpers\ApiResponse;

class ExceptionConfigurator
{
    public static function configure(Exceptions $exceptions): void
    {
        // レンダリング処理
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            return ApiResponse::error(
                __('auth.unauthenticated'),
                null,
                Response::HTTP_FORBIDDEN
            );
        });

        $exceptions->renderable(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            return ApiResponse::error(
                __('project.rate_limit_error'),
                null,
                Response::HTTP_TOO_MANY_REQUESTS
            );
        });

        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e, Request $request) {
            return ApiResponse::error(
                __('project.validation_error'),
                $e->errors(),
                Response::HTTP_BAD_REQUEST
            );
        });

        $exceptions->renderable(function (Throwable $e, Request $request) {
            return ApiResponse::error(
                __('project.unexpected_error'),
                [
                    'exception' => class_basename($e),
                    'message' => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        });
    }
}
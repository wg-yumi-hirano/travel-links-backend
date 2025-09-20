<?php declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
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
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return ApiResponse::error(
                __('auth.unauthenticated'),
                $e->errors(),
                Response::HTTP_FORBIDDEN
            );
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            return ApiResponse::error(
                __('project.rate_limit_error'),
                null,
                Response::HTTP_TOO_MANY_REQUESTS
            );
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return ApiResponse::error(
                __('project.validation_error'),
                $e->errors(),
                Response::HTTP_BAD_REQUEST
            );
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            return ApiResponse::error(
                __('project.unexpected_error'),
                [
                    'exception' => class_basename($e),
                    'message' => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        });

        // レポート処理
        $exceptions->report(function (Throwable $e) {
            Log::channel('stack_error')->error($e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
            ]);
        });
    }
}
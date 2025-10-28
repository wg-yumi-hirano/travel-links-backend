<?php declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use App\Helpers\ApiResponse;

class ExceptionConfigurator
{
    public static function configure(Exceptions $exceptions): void
    {
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            return ApiResponse::error(
                __('auth.unauthenticated'),
                null,
                Response::HTTP_UNAUTHORIZED
            );
        });

        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            // frontend 経由であれば発生しないはず
            self::warning('Unauthorized', $e, $request);

            return ApiResponse::error(
                __('auth.unauthorized'),
                null,
                Response::HTTP_FORBIDDEN
            );
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, Request $request) {
            // frontend 経由であれば発生しないはず
            self::warning('Unauthorized', $e, $request);

            return ApiResponse::error(
                __('auth.unauthorized'),
                null,
                Response::HTTP_FORBIDDEN
            );
        });

        $exceptions->renderable(function (\Symfony\Component\Mailer\Exception\TransportException $e, Request $request) {
            self::error('Failed to send email', $e);

            return ApiResponse::error(
                __('project.send_email_error'),
                null,
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        });

        $exceptions->renderable(function (\Illuminate\Routing\Exceptions\InvalidSignatureException $e, Request $request) {
            self::warning('Failed to verify email', $e, $request);

            return ApiResponse::error(
                __('project.failed_verification_email'),
                null,
                Response::HTTP_BAD_REQUEST
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
            self::error('Unexpected error', $e);

            return ApiResponse::error(
                __('project.unexpected_error'),
                null,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        });
    }

    private static function warning(string $message, Throwable $e, Request $request) {
        Log::warning($message, [
            'message' => $e->getMessage(),
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'input' => $request->except(['password', 'token']), // センシティブな情報を除外
            'referer' => $request->headers->get('referer'),
        ]);
    }

    private static function error(string $message, Throwable $e) {
        Log::error($message, [
            'exception' => $e,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode(),
        ]);
    }
}
<?php declare(strict_types=1);

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ApiResponse;

class Handler extends ExceptionHandler
{
    /**
     * レポート対象外の例外
     */
    protected $dontReport = [
        //
    ];

    /**
     * バリデーションエラーで除外する入力フィールド
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * 例外のレポート処理（ログ出力）
     */
    public function report(Throwable $exception): void
    {
        Log::channel('stack_error')->error($exception->getMessage(), [
            'exception' => $exception,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
        ]);
    }

    /**
     * レスポンスのレンダリング（JSON統一）
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof AuthenticationException) {
                return ApiResponse::error(
                    __('auth.unauthenticated'),
                    $exception->errors(),
                    Response::HTTP_FORBIDDEN
                );
            } else if ($exception instanceof ThrottleRequestsException) {
                return response()->json([
                    'message' => __('auth.throttle'),
                    'data' => null,
                    'errors' => ['auth' => [__('auth.throttle')]],
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }  else if ($exception instanceof ValidationException) {
                return ApiResponse::error(
                    __('project.validation_error'),
                    $exception->errors(),
                    Response::HTTP_BAD_REQUEST
                );
            }

            return ApiResponse::error(
                __('project.unexpected_error'),
                [
                    'exception' => class_basename($exception),
                    'message' => $exception->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return parent::render($request, $exception);
    }
}
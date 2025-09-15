<?php declare(strict_types=1);

namespace App\Helpers;

class ApiResponse
{
    public static function success(string $message, $data = null, int $status = 200)
    {
        return response()->json(
            [
                'message' => $message,
                'data' => $data,
                'errors' => null,
            ],
            $status,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public static function error(string $message, $errors = null, int $status = 400)
    {
        return response()->json(
            [
                'message' => $message,
                'data' => null,
                'errors' => $errors,
            ],
            $status,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
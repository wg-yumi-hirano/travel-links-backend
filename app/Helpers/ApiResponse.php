<?php declare(strict_types=1);

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = null, int $status = 200)
    {
        return response()->json(
            $data,
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
                'errors' => $errors,
            ],
            $status,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
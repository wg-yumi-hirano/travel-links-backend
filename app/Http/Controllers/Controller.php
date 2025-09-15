<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Helpers\ApiResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success(string $message, $data = null, int $status = 200)
    {
        return ApiResponse::success($message, $data, $status);
    }

    protected function error(string $message, $errors = null, int $status = 400)
    {
        return ApiResponse::error($message, $errors, $status);
    }
}
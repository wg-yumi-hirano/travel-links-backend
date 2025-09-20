<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends \App\Http\Controllers\Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => '未認証です',
                'data' => null,
                'errors' => ['auth' => ['ログインしていません']],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'message' => __('project.user_show'),
            'data' => $user,
            'errors' => null,
        ]);
    }
}
<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\ChangePasswordRequest;

class UserController extends \App\Http\Controllers\Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error(
                __('project.not_authorized'),
                null,
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->success($user);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error(
                __('project.invalid_current_password'),
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return $this->success();
    }
}
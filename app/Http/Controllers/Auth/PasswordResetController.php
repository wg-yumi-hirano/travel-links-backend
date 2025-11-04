<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\SendResetLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;

class PasswordResetController extends \App\Http\Controllers\Controller
{
    public function sendResetLinkEmail(SendResetLinkRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? $this->success()
            : $this->error(__($status), null, Response::HTTP_BAD_REQUEST);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->success()
            : $this->error(__($status), null, Response::HTTP_BAD_REQUEST);
    }
}
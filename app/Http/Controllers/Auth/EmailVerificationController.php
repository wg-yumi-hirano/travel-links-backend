<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EmailVerificationController extends \App\Http\Controllers\Controller
{
    public function verify(Request $request, $id, $hash): JsonResponse
    {
        $user = User::find($id);

        if (! $user || ! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return $this->error(
                __('project.email_invalid_verification_parameters'),
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error(
                __('project.email_already_verified'),
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->markEmailAsVerified();

        return $this->success();
    }

    public function resend(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->error(
                __('project.failed_send_verification_email_due_to_user_not_found'),
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error(
                __('project.email_already_verified'),
                null,
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->sendEmailVerificationNotification();
        return $this->success();
    }
}

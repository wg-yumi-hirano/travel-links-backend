<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends \App\Http\Controllers\Controller
{
    public function show()
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
}
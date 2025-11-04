<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;

class RegisterController extends \App\Http\Controllers\Controller
{
    public function __invoke(RegisterRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user->notify(new VerifyEmail()); // 通知送信（VerifyEmail）
        });
    }
}

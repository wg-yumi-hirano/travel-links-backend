<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create([
            'login_id' => $request->login_id,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user); // Cookieベースでログイン

        return response()->json(['message' => 'Registered and logged in'], 201);
    }
}

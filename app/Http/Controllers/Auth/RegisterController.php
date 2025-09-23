<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;

class RegisterController extends \App\Http\Controllers\Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->success(__('project.register_success'));
    }
}

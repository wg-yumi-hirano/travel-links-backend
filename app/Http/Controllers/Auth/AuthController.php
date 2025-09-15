<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->error('ログインに失敗しました', ['auth' => ['認証情報が正しくありません']], 401);
        }

        $request->session()->regenerate();

        return $this->success('ログイン成功', Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();         // セッションIDを無効化
        $request->session()->regenerateToken();    // CSRFトークンを再生成

        return $this->success('ログアウトしました', Auth::user());
    }
}

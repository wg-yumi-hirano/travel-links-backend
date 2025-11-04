<?php declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ResetPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            //TODO 使用しなければならない文字を定義する
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
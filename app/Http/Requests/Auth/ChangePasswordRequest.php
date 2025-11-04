<?php declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class ChangePasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'current_password' => 'required',
            //TODO 使用しなければならない文字を定義する
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
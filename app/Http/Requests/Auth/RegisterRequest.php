<?php declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認証不要で誰でも使える
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //TODO 利用可能な文字を制限する（全角禁止）
            'login_id' => 'required|string|min:8|unique:users,login_id',
            //TODO 使用しなければならない文字を定義する
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}

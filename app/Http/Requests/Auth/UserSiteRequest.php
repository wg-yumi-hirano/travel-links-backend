<?php declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'url' => 'required|url|max:8000',
            'address' => 'required|string|max:100',
            'thumbnail' => 'nullable|string',
            'description' => 'nullable|string|max:1000',
            'price_min' => 'required|integer|min:1',
            'price_max' => 'required|integer|gte:price_min',
        ];
    }
}
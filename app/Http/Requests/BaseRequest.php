<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    protected function castToInt(array $keys): void
    {
        $casted = [];

        foreach ($keys as $key) {
            $value = $this->input($key);
            $casted[$key] = is_numeric($value) ? (int) $value : $value;
        }

        $this->merge($casted);
    }
}
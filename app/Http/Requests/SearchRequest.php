<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可は不要
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
            'keyword' => ['nullable', 'string'],
            'sort' => ['nullable', 'in:price_min_asc,updated_at_desc'],
        ];
    }

    public function attributes(): array
    {
        return [
            'page' => __('pagination.page'),
            'per_page' => __('pagination.per_page'),
            'keyword' => __('search.keyword'),
            'sort' => __('search.sort'),
        ];
    }
}
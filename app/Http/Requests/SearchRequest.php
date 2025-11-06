<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

class SearchRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
            'keyword' => ['nullable', 'string'],
            'sort' => ['nullable', 'in:price_min_asc,updated_at_desc'],
            'budget_min' => ['nullable', 'integer', 'min:1', 'max:100000000'],
            'budget_max' => ['nullable', 'integer', 'min:1', 'max:100000000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'page' => __('pagination.page'),
            'per_page' => __('pagination.per_page'),
            'keyword' => __('project.keyword'),
            'sort' => __('project.sort'),
            'budget_min' =>  __('project.budget_min'),
            'budget_max' =>  __('project.budget_max'),
        ];
    }
}
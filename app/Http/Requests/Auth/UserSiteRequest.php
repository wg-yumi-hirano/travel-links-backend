<?php declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use App\Http\Requests\BaseRequest;
use App\Services\ImageService;

class UserSiteRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'min:1'],
            'url' => ['required', 'url', 'max:8000', 'min:1'],
            'address' => ['required', 'string', 'max:100', 'min:1'],
            'thumbnail' => [
                'nullable',
                'string',
                'regex:/^data:image\/(jpeg|png|webp);base64,/',
                function ($attribute, $value, $fail) {
                    $imageService = app(ImageService::class);
                    $ret = $imageService->decode($value);
                    if ($ret === false) {
                        return $fail(__('project.thumbnail_decode_error'));
                    }
                    $image = (object) $ret;
                    if (Str::length($image->binary) > 200 * 1024) {
                        return $fail(Lang::get('project.thumbnail_too_large', ['size' => 200]));
                    }
                },
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_min' => ['required', 'integer', 'min:1', 'max:100000000'],
            'price_max' => ['required', 'integer', 'min:1', 'max:100000000', 'gte:price_min'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('project.name'),
            'url' =>  __('project.url'),
            'address' =>  __('project.address'),
            'thumbnail' =>  __('project.thumbnail'),
            'description' =>  __('project.description'),
            'price_min' =>  __('project.price_min'),
            'price_max' =>  __('project.price_max'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->castToInt(['price_min', 'price_max']);
    }
}
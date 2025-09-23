<?php declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'address' => $this->address,
            'description' => $this->description,
            'price_min' => $this->price_min,
            'price_max' => $this->price_max,
            'updated_at' => $this->updated_at?->toIso8601String()
        ];
    }
}

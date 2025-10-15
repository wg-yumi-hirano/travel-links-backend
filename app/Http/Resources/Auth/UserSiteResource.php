<?php declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\ImageService;

class UserSiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $base64 = null;
        if ($this->thumbnail) {
            $imageService = app(ImageService::class);
            $base64 = $imageService->encode($this->thumbnail);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'address' => $this->address,
            'thumbnail' => $base64,
            'description' => $this->description,
            'price_max' => $this->price_max,
            'price_min' => $this->price_min,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String()
        ];
    }
}

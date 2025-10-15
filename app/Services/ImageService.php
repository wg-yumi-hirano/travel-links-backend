<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use RuntimeException;
use App\Models\Image;

class ImageService
{
    /**
     * base64文字列をモデル用データに変換
     *
     * @param string $base64
     * @return string バイナリデータ
     * @throws RuntimeException
     */
    public function decode(string $base64): array|false
    {
        [$meta, $base64] = explode(',', $base64, 2);

        preg_match('/^data:(image\/\w+);base64/', $meta, $matches);
        $mime_type = $matches[1];

        $binary = base64_decode($base64, true);
        if ($binary === false) {
            return false;
        }

        return [
            "mime_type" => $mime_type,
            "binary" => $binary
        ];
    }

    /**
     * Imageモデルからbase64形式のData URIを生成
     *
     * @param Image $image
     * @return string|null
     */
    public function encode(Image $image): ?string
    {
        if (is_null($image->binary) || is_null($image->mime_type)) {
            return null;
        }

        $base64 = base64_encode($image->binary);

        return "data:{$image->mime_type};base64,{$base64}";
    }
}

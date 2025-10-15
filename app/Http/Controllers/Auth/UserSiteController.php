<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\UserSiteRequest;
use App\Models\Image;
use App\Models\Site;
use App\Services\ImageService;

class UserSiteController extends \App\Http\Controllers\Controller
{
    public function viewAny(Request $request): JsonResponse
    {
        $sites = Site::query()
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->with('thumbnail')
            ->orderBy('id', 'asc')
            ->get();

        return $this->success(\App\Http\Resources\Auth\UserSiteResource::collection($sites));
    }

    public function create(UserSiteRequest $request, ImageService $service): JsonResponse
    {
        DB::transaction(function () use ($request, $service): void {
            $image = $request->thumbnail
                ? Image::create($service->decode($request->thumbnail))
                : null;

            $site = new Site($request->validated());
            $site->user_id = Auth::id();
            $site->thumbnail_id = $image?->id;
            $site->save();
        });

        return $this->success();
    }

    public function update(UserSiteRequest $request, Site $site, ImageService $service): JsonResponse
    {
        $this->authorize('update', $site);

        DB::transaction(function () use ($request, $site, $service): void {
            $inputBase64 = $request->thumbnail;
            $currentImage = $site->thumbnail;
            $currentBase64 = $currentImage ? $service->encode($currentImage) : null;

            if ($currentBase64) {
                if ($inputBase64 === null) {
                    $site->thumbnail_id = null;
                } elseif ($inputBase64 !== $currentBase64) {
                    $currentImage->update($service->decode($inputBase64));
                }
            } elseif ($inputBase64 !== null) {
                $newImage = Image::create($service->decode($inputBase64));
                $site->thumbnail_id = $newImage->id;
            }

            $site->fill($request->validated());
            $site->save();
            if ($currentImage && $site->thumbnail_id === null) {
                $currentImage->forceDelete();  // 物理削除
            }
        });

        return $this->success();
    }

    public function delete(Site $site): JsonResponse
    {
        $this->authorize('delete', $site);

        $site->delete();

        return $this->success();
    }
}
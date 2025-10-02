<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\UserSiteRequest;
use App\Models\Image;
use App\Models\Site;

class UserSiteController extends \App\Http\Controllers\Controller
{
    public function viewAny(Request $request): JsonResponse
    {
        $sites = Site::query()
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->with('thumbnail')
            ->get();

        return $this->success(\App\Http\Resources\Auth\UserSiteResource::collection($sites));
    }

    public function create(UserSiteRequest $request): JsonResponse
    {
        $image = $request->thumbnail
            ? Image::create(['base64' => $request->thumbnail])
            : null;

        $site = new Site($request->validated());
        $site->user_id = Auth::id();
        $site->thumbnail_id = $image?->id;
        $site->save();

        return $this->success();
    }

    public function update(UserSiteRequest $request, Site $site): JsonResponse
    {
        $this->authorize('update', $site);

        $thumbnailInput = $request->thumbnail;
        $currentImage = $site->thumbnail;

        if ($currentImage) {
            if ($thumbnailInput === null) {
                $currentImage->delete();
                $site->thumbnail_id = null;
            } elseif ($thumbnailInput !== $currentImage->base64) {
                $currentImage->update(['base64' => $thumbnailInput]);
            }
        } elseif ($thumbnailInput !== null) {
            $newImage = Image::create(['base64' => $thumbnailInput]);
            $site->thumbnail_id = $newImage->id;
        }

        $site->fill($request->validated());
        $site->save();

        return $this->success();
    }

    public function delete(Site $site): JsonResponse
    {
        $this->authorize('delete', $site);

        $site->delete();

        return $this->success();
    }
}
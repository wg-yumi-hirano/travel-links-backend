<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Models\Site;

class SiteController extends Controller
{
    public function thumbnail(Site $site)
    {
        $image = $site->thumbnail;

        if (! $image) {
            return $this->error(
                __('project.image_not_found'),
                null,
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json([
            'base64' => $image->base64,
        ]);
    }
}

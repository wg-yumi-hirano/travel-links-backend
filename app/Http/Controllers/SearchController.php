<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\SearchResource;
use App\Models\Site;

class SearchController extends Controller
{
    public function viewAny(SearchRequest $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', Config::get('project.search_per_page', 10));
        $keyword = $request->input('keyword');
        $sort = $request->input('sort');

        $query = Site::query()->whereNull('deleted_at');

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        switch ($sort) {
            case 'price_min_asc':
                $query->orderBy('price_min', 'asc');
                break;
            case 'updated_at_desc':
                $query->orderBy('updated_at', 'desc');
                break;
            default:
                $query->orderByDesc('id');
        }

        $sites = $query->paginate($perPage);

        return $this->success([
            'data' => SearchResource::collection($sites),
            'pagination' => [
                'current_page' => $sites->currentPage(),
                'last_page' => $sites->lastPage(),
                'per_page' => $sites->perPage(),
                'total' => $sites->total(),
            ],
        ]);
    }
}

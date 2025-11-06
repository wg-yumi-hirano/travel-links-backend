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
        $budgetMin = $request->input('budget_min');
        $budgetMax = $request->input('budget_max');
        $sort = $request->input('sort');

        $query = Site::query();

        if (!empty($keyword)) {
            $split = preg_split('/\s+/', $keyword);
            $list = array_filter($split, fn($word) => $word !== '');
            foreach ($list as $k) {
                $query->where(function ($q) use ($k) {
                    $q->where('name', 'like', "%{$k}%")
                    ->orWhere('address', 'like', "%{$k}%")
                    ->orWhere('description', 'like', "%{$k}%");
                });
            }
        }

        if (!empty($budgetMin)) {
            $query->where('price_max', '>=', $budgetMin);
        }
        if (!empty($budgetMax)) {
            $query->where('price_min', '<=', $budgetMax);
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

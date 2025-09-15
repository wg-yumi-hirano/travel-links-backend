<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $dummyData = [
            [
                'id' => 1,
                'name' => 'ダミーサイト',
                'thumbnailUrl' => 'https://google.com',
                'description' => 'ダミー説明',
                'priceMin' => 0,
                'priceMax' => 1234,
            ],
        ];

        return $this->success('検索結果を取得しました', $dummyData);
    }
}

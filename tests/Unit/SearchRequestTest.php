<?php declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Str;
use App\Http\Requests\SearchRequest;
use App\Models\Image;
use App\Services\ImageService;

class SearchRequestTest extends TestCase
{
    public function testPositive_without_any_parameters()
    {
        $validator = $this->createValidator([]);

        $this->assertTrue($validator->passes());
    }

    public function testPositive_with_all_parameters()
    {
        $validator = $this->createValidator([
            'page' => 10,
            'per_page' => 25,
            'keyword' => 'キーワード',
            'sort' => 'price_min_asc',
            'budget_min' => 1000,
            'budget_max' => 10000,
        ]);

        $this->assertTrue($validator->passes());
    }

    public function testPositive_set_min_values()
    {
        $validator = $this->createValidator([
            'page' => 1,
            'per_page' => 1,
            'budget_min' => 1,
            'budget_max' => 1,
        ]);

        $this->assertTrue($validator->passes());
    }

    public function testPositive_set_max_values()
    {
        $validator = $this->createValidator([
            'per_page' => 50,
            'budget_min' => 100000000,
            'budget_max' => 100000000,
        ]);

        $this->assertTrue($validator->passes());
    }

    public function testPositive_set_variety_of_sort()
    {
        $validator = $this->createValidator(['sort' => 'price_min_asc']);
        $this->assertTrue($validator->passes());

        $validator = $this->createValidator(['sort' => 'updated_at_desc']);
        $this->assertTrue($validator->passes());
    }

    public function testNegative_with_smaller_values()
    {
        $validator = $this->createValidator([
            'page' => 0,
            'per_page' => 0,
            'budget_min' => 0,
            'budget_max' => 0,
        ]);

        $this->assertFalse($validator->passes());

        $actualKeys = $validator->errors()->keys();

        $this->assertContains('page', $actualKeys);
        $this->assertContains('per_page', $actualKeys);
        $this->assertContains('budget_min', $actualKeys);
        $this->assertContains('budget_max', $actualKeys);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['page', 'per_page', 'budget_min', 'budget_max'];
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_larger_values()
    {
        $validator = $this->createValidator([
            'per_page' => 51,
            'budget_min' => 100000001,
            'budget_max' => 100000001,
        ]);

        $this->assertFalse($validator->passes());

        $actualKeys = $validator->errors()->keys();

        $this->assertContains('per_page', $actualKeys);
        $this->assertContains('budget_min', $actualKeys);
        $this->assertContains('budget_max', $actualKeys);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['per_page', 'budget_min', 'budget_max'];
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_invalid_data_type()
    {
        $validator = $this->createValidator([
            'page' => 'invalid',
            'per_page' => 'invalid',
            'budget_min' => 'invalid',
            'budget_max' => 'invalid',
        ]);

        $this->assertFalse($validator->passes());

        $actualKeys = $validator->errors()->keys();

        $this->assertContains('page', $actualKeys);
        $this->assertContains('per_page', $actualKeys);
        $this->assertContains('budget_min', $actualKeys);
        $this->assertContains('budget_max', $actualKeys);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['page', 'per_page', 'budget_min', 'budget_max'];
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_invalid_sort()
    {
        $validator = $this->createValidator([
            'sort' => 'invalid',
        ]);

        $this->assertFalse($validator->passes());

        $actualKeys = $validator->errors()->keys();

        $this->assertContains('sort', $actualKeys);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['sort'];
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    private function createValidator($parameter): \Illuminate\Validation\Validator
    {
        $req = new SearchRequest();
        return \Illuminate\Support\Facades\Validator::make($parameter, $req->rules(), $req->messages(), $req->attributes());
    }
}
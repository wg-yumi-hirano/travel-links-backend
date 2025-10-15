<?php declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Str;
use App\Http\Requests\Auth\UserSiteRequest;
use App\Models\Image;
use App\Services\ImageService;

class UserSiteRequestTest extends TestCase
{
    public function testPositive_set_all_parameters()
    {
        $validator = $this->createValidator([
            'name' => 'テストサイト',
            'url' => 'https://example.com',
            'address' => '東京都渋谷区',
            'thumbnail' => 'data:image/jpeg;base64,',
            'description' => '説明文',
            'price_min' => 1,
            'price_max' => 1,
        ]);

        $this->assertTrue($validator->passes());
    }

    public function testPositive_set_max_values()
    {
        // ジャストなサイズを設定するのは難しい
        $image = Image::factory()->create($this->generateImage(211 * 1024));
        $imageService = new ImageService();
        $base64 = $imageService->encode($image);

        $request = new UserSiteRequest();
        $validator = $this->createValidator([
            'name' => str_repeat('あ', 100),
            'url' => 'https://example.com/' . str_repeat('a', 8000 - Str::length('https://example.com/')),
            'address' => str_repeat('東', 100),
            'description' => str_repeat('説', 1000),
            'thumbnail' => $base64,
            'price_min' => 100000000,
            'price_max' => 100000000,
        ]);

        $this->assertTrue($validator->passes());
    }

    public function testPositive_set_variety_of_thumbnail()
    {
        $parameters = [
            'name' => 'テストサイト',
            'url' => 'https://example.com',
            'address' => '東京都渋谷区',
            'thumbnail' => null,
            'description' => '説明文',
            'price_min' => 1,
            'price_max' => 1,
        ];
        $request = new UserSiteRequest();

        $parameters['thumbnail'] = 'data:image/jpeg;base64,';
        $validator = $this->createValidator($parameters, $request->rules());
        $this->assertTrue($validator->passes());

        $parameters['thumbnail'] = 'data:image/png;base64,';
        $validator = $this->createValidator($parameters, $request->rules());
        $this->assertTrue($validator->passes());

        $parameters['thumbnail'] = 'data:image/webp;base64,';
        $validator = $this->createValidator($parameters, $request->rules());
        $this->assertTrue($validator->passes());
    }

    public function testNegative_without_required_parameters()
    {
        $request = new UserSiteRequest();
        $validator = $this->createValidator([]);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->keys();

        $this->assertContains('name', $errors);
        $this->assertContains('url', $errors);
        $this->assertContains('address', $errors);
        $this->assertContains('price_min', $errors);
        $this->assertContains('price_max', $errors);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['name', 'url', 'address', 'price_min', 'price_max'];
        $actualKeys = $validator->errors()->keys();
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_empty()
    {
        $request = new UserSiteRequest();
        $validator = $this->createValidator([
            'name' => '',
            'url' => '',
            'address' => '',
            'thumbnail' => '',
            'description' => '',
            'price_min' => 1,
            'price_max' => 1,
        ]);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->keys();

        $this->assertContains('name', $errors);
        $this->assertContains('url', $errors);
        $this->assertContains('address', $errors);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['name', 'url', 'address'];
        $actualKeys = $validator->errors()->keys();
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_only_space()
    {
        $request = new UserSiteRequest();
        $validator = $this->createValidator([
            'name' => str_repeat(' ', 100),
            'url' => str_repeat(' ', 8000),
            'address' => str_repeat(' ', 100),
            'thumbnail' => str_repeat(' ', 1000),
            'description' => str_repeat(' ', 1000),
            'price_min' => 1,
            'price_max' => 1,
        ]);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->keys();

        $this->assertContains('name', $errors);
        $this->assertContains('url', $errors);
        $this->assertContains('address', $errors);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['name', 'url', 'address'];
        $actualKeys = $validator->errors()->keys();
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_smaller_values()
    {
        $request = new UserSiteRequest();
        $validator = $this->createValidator([
            'name' => 'テストサイト',
            'url' => 'https://example.com',
            'address' => '東京都渋谷区',
            'thumbnail' => null,
            'description' => '',
            'price_min' => 0,
            'price_max' => 0,
        ]);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->keys();

        $this->assertContains('price_min', $errors);
        $this->assertContains('price_max', $errors);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['price_min', 'price_max'];
        $actualKeys = $validator->errors()->keys();
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_larger_values()
    {
        // ジャストなサイズを設定するのは難しい
        $image = Image::factory()->create($this->generateImage(212 * 1024));
        $imageService = new ImageService();
        $base64 = $imageService->encode($image);

        $request = new UserSiteRequest();
        $validator = $this->createValidator([
            'name' => str_repeat('あ', 101),
            'url' => 'https://example.com/' . str_repeat('a', 8000 - Str::length('https://example.com/') + 1),
            'address' => str_repeat('東', 101),
            'description' => str_repeat('説', 1001),
            'thumbnail' => $base64,
            'price_min' => 100000001,
            'price_max' => 100000001,
        ]);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->keys();

        $this->assertContains('name', $errors);
        $this->assertContains('url', $errors);
        $this->assertContains('address', $errors);
        $this->assertContains('description', $errors);
        $this->assertContains('thumbnail', $errors);
        $this->assertContains('price_min', $errors);
        $this->assertContains('price_max', $errors);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['name', 'url', 'address', 'description', 'thumbnail', 'price_min', 'price_max'];
        $actualKeys = $validator->errors()->keys();
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_invalid_thumbnail_extension()
    {
        $request = new UserSiteRequest();
        $validator = $this->createValidator([
            'name' => 'テストサイト',
            'url' => 'https://example.com',
            'address' => '東京都渋谷区',
            'thumbnail' => 'data:image/invalid;base64,',
            'description' => '説明文',
            'price_min' => 1,
            'price_max' => 1,
        ]);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->keys();

        $this->assertContains('thumbnail', $errors);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['thumbnail'];
        $actualKeys = $validator->errors()->keys();
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    public function testNegative_with_multiple_conditions()
    {
        $request = new UserSiteRequest();
        $validator = $this->createValidator([
            'name' => 'テストサイト',
            'url' => 'https://example.com',
            'address' => '東京都渋谷区',
            'thumbnail' => null,
            'description' => '説明文',
            'price_min' => 2,
            'price_max' => 1,
        ]);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->keys();

        $this->assertContains('price_max', $errors);

        // 含まれてはいけないキーが存在しないか
        $expectedKeys = ['price_max'];
        $actualKeys = $validator->errors()->keys();
        $this->assertEqualsCanonicalizing($expectedKeys, $actualKeys);

        dump($validator->errors()->all());
    }

    private function createValidator($parameter): \Illuminate\Validation\Validator
    {
        $req = new UserSiteRequest();
        return \Illuminate\Support\Facades\Validator::make($parameter, $req->rules(), $req->messages(), $req->attributes());
    }

    private function generateImage(int $sizeByte): array
    {
        // JPEGヘッダー + ダミーデータ
        return [
            'mime_type' => 'image/jpeg',
            'binary' => "\xFF\xD8\xFF\xE0" . str_repeat("\x00", 1024) . random_bytes($sizeByte - 1024 - 4)
        ];
    }
}
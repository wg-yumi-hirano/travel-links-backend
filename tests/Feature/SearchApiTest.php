<?php declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use App\Models\Site;

class SearchApiTest extends BaseTestCase
{
    use RefreshDatabase;

    /* 正常系 */
    public function testPositive_pagination()
    {
        Site::factory()->count(20)->create();
        Site::factory()->create()->delete(); // 削除済み

        $response = $this->getApi('/api/search?per_page=10');
        $response->assertStatus(200)
                 ->assertJsonPath('pagination.current_page', 1)
                 ->assertJsonPath('pagination.last_page', 2)
                 ->assertJsonPath('pagination.per_page', 10)
                 ->assertJsonPath('pagination.total', 20);
        
        $response = $this->getApi('/api/search?per_page=10&page=2');
        $response->assertStatus(200)
                 ->assertJsonPath('pagination.current_page', 2)
                 ->assertJsonPath('pagination.last_page', 2)
                 ->assertJsonPath('pagination.per_page', 10)
                 ->assertJsonPath('pagination.total', 20);
    }

    public function testPositive_keyword()
    {
        Site::factory()->create(['name' => 'キーワード', 'address' => 'キーワード']);
        Site::factory()->create(['name' => 'キー', 'address' => 'キーワード']);
        Site::factory()->create(['name' => 'キーワード', 'address' => 'キー']);
        Site::factory()->create(['name' => 'キー', 'address' => 'キー']);

        $response = $this->getApi('/api/search?per_page=4&keyword=ワー');
        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
        
        $response = $this->getApi('/api/search?per_page=4&keyword=ワー あ');
        $response->assertStatus(200)
                 ->assertJsonCount(0, 'data');
    }

    public function testPositive_budget()
    {
        Site::factory()->create(['price_min' => 1000, 'price_max' => 2000]);
        Site::factory()->create(['price_min' => 1500, 'price_max' => 2500]);
        Site::factory()->create(['price_min' => 2000, 'price_max' => 3000]);
        Site::factory()->create(['price_min' => 2500, 'price_max' => 3500]);

        $response = $this->getApi('/api/search?per_page=4&budget_min=2001');
        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
        $prices = collect($response->json('data'))->pluck('price_max')->all();
        $this->assertEqualsCanonicalizing([2500, 3000, 3500], $prices);
        
        $response = $this->getApi('/api/search?per_page=4&budget_max=2499');
        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
        $prices = collect($response->json('data'))->pluck('price_min')->all();
        $this->assertEqualsCanonicalizing([1000, 1500, 2000], $prices);

        $response = $this->getApi('/api/search?per_page=4&budget_min=2001&budget_max=2499');
        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
        $maxes = collect($response->json('data'))->pluck('price_max')->all();
        $this->assertEqualsCanonicalizing([2500, 3000], $maxes);
        $mins = collect($response->json('data'))->pluck('price_min')->all();
        $this->assertEqualsCanonicalizing([1500, 2000], $mins);
    }

    public function testPositive_sort()
    {
        Site::factory()->create(['price_min' => 3000, 'updated_at' => Carbon::parse('2023-01-01')]);
        Site::factory()->create(['price_min' => 1000, 'updated_at' => Carbon::parse('2024-01-01')]);
        Site::factory()->create(['price_min' => 2000, 'updated_at' => Carbon::parse('2025-01-01')]);

        $response = $this->getApi('/api/search?per_page=3&sort=price_min_asc');
        $response->assertStatus(200);
        $prices = collect($response->json('data'))->pluck('price_min')->all();
        $this->assertEquals([1000, 2000, 3000], $prices);

        $response = $this->getApi('/api/search?per_page=3&sort=updated_at_desc');
        $response->assertStatus(200);
        $timestamps = collect($response->json('data'))->pluck('updated_at')->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))->all();
        $this->assertEquals(['2025-01-01', '2024-01-01', '2023-01-01'], $timestamps);
    }

    /* 異常系 */
    public function testNegative_page()
    {
        $response = $this->getApi('/api/search?page=invalid');
        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['page']);
        $this->assertEquals(['page'], array_keys($response->json('errors')));
    }

    public function testNegative_per_page()
    {
        $response = $this->getApi('/api/search?per_page=invalid');
        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['per_page']);
        $this->assertEquals(['per_page'], array_keys($response->json('errors')));
    }

    public function testNegative_budget()
    {
        $response = $this->getApi('/api/search?budget_min=invalid');
        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['budget_min']);
        $this->assertEquals(['budget_min'], array_keys($response->json('errors')));

        $response = $this->getApi('/api/search?budget_max=invalid');
        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['budget_max']);
        $this->assertEquals(['budget_max'], array_keys($response->json('errors')));
    }

    public function testNegative_sort()
    {
        $response = $this->getApi('/api/search?sort=invalid');
        $response->assertStatus(400)
                 ->assertJsonValidationErrors(['sort']);
        $this->assertEquals(['sort'], array_keys($response->json('errors')));
    }
}

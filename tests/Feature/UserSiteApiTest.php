<?php declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Site;
use App\Models\Image;
use App\Services\ImageService;

class UserSiteApiTest extends BaseTestCase
{
    use RefreshDatabase;

    public function testPositive_get_user_sites_returns_empty_list()
    {
        $user = User::factory()->create();
        Site::factory()->create(['user_id' => $user->id])->delete(); // 削除済み
        Site::factory()->create(); // 他人のサイト

        $res = $this->getAuthApi('/api/user/sites', $user);

        $res->assertOk()
            ->assertJsonCount(0);
    }

    public function testPositive_get_user_sites_returns_multiple_sites()
    {
        $user = User::factory()->create();
        $image = Image::factory()->create();
        $siteWithThumbnail = Site::factory()->create([
            'user_id' => $user->id,
            'thumbnail_id' => $image->id,
        ]);
        $siteWithoutThumbnail = Site::factory()->create([
            'user_id' => $user->id,
            'thumbnail_id' => null,
        ]);
        Site::factory()->create(); // 他ユーザーのサイト

        $res = $this->getAuthApi('/api/user/sites', $user);

        $res->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'id' => $siteWithThumbnail->id,
                'thumbnail' => $image->base64,
            ])
            ->assertJsonFragment([
                'id' => $siteWithoutThumbnail->id,
                'thumbnail' => null,
            ]);
    }

    public function testPositive_post_user_sites_creates_record()
    {
        $user = User::factory()->create();

        $this->postAuthApi('/api/user/sites', [
            'name' => 'テストサイト',
            'url' => 'https://example.com',
            'address' => '東京都渋谷区',
            'thumbnail' => 'data:image/jpeg;base64,',
            'description' => '説明文',
            'price_min' => 5000,
            'price_max' => 10000,
        ], $user)
        ->assertOk();

        $this->assertDatabaseHas('sites', [
            'user_id' => $user->id,
            'name' => 'テストサイト',
            'url' => 'https://example.com',
            'address' => '東京都渋谷区',
            'description' => '説明文',
            'price_min' => 5000,
            'price_max' => 10000,
        ]);

        $site = Site::where('user_id', $user->id)->first();
        $image = Image::first();
        $this->assertEquals($site->thumbnail_id, $image->id);

        $imageService = new ImageService();
        $base64 = $imageService->encode($image);
        $this->assertEquals('data:image/jpeg;base64,', $base64);
    }

    public function testPositive_put_user_sites_updates_record()
    {
        $imageService = new ImageService();
        $user = User::factory()->create();
        $image = Image::factory()->create($imageService->decode('data:image/jpeg;base64,old'));
        $site = Site::factory()->create([
            'user_id' => $user->id,
            'thumbnail_id' => $image->id,
        ]);

        $this->putAuthApi("/api/user/sites/{$site->id}", [
            'name' => ' 更新後サイト ',
            'url' => ' https://updated.com ',
            'address' => ' 東京都港区 ',
            'thumbnail' => 'data:image/png;base64,new',
            'description' => ' 更新済み ',
            'price_min' => 6000,
            'price_max' => 12000,
        ], $user)
        ->assertOk();

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'name' => '更新後サイト',
            'url' => 'https://updated.com',
            'description' => '更新済み',
            'price_min' => 6000,
            'price_max' => 12000,
        ]);
        $this->assertEquals($image->id, $site->fresh()->thumbnail_id);

        $base64 = $imageService->encode($image->fresh());
        $this->assertEquals('data:image/png;base64', explode(',', $base64, 2)[0]);
    }

    public function testPositive_update_site_removes_existing_thumbnail_when_input_is_null()
    {
        $user = User::factory()->create();
        $image = Image::factory()->create();
        $site = Site::factory()->create([
            'user_id' => $user->id,
            'thumbnail_id' => $image->id,
        ]);

        $this->putAuthApi("/api/user/sites/{$site->id}", [
            'name' => $site->name,
            'url' => $site->url,
            'address' => $site->address,
            'thumbnail' => null,
            'description' => $site->description,
            'price_min' => $site->price_min,
            'price_max' => $site->price_max,
        ], $user)
        ->assertOk();

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        $this->assertNull($site->fresh()->thumbnail_id);
    }

    public function testPositive_update_site_creates_new_thumbnail_when_none_existed()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create([
            'user_id' => $user->id,
            'thumbnail_id' => null,
        ]);

        $this->putAuthApi("/api/user/sites/{$site->id}", [
            'name' => $site->name,
            'url' => $site->url,
            'address' => $site->address,
            'thumbnail' => 'data:image/jpeg;base64,',
            'description' => $site->description,
            'price_min' => $site->price_min,
            'price_max' => $site->price_max,
        ], $user)
        ->assertOk();

        $site = $site->fresh();
        $this->assertNotNull($site->thumbnail_id);

        $imageService = new ImageService();
        $base64 = $imageService->encode($site->thumbnail);
        $this->assertEquals('data:image/jpeg;base64,', $base64);
    }

    public function testPositive_update_site_keeps_existing_thumbnail_when_value_is_same()
    {
        $imageService = new ImageService();
        $user = User::factory()->create();
        $image = Image::factory()->create($imageService->decode('data:image/jpeg;base64,same'));
        $site = Site::factory()->create([
            'user_id' => $user->id,
            'thumbnail_id' => $image->id,
        ]);
        $image_updated_at = $image->updated_at;

        $this->putAuthApi("/api/user/sites/{$site->id}", [
            'name' => $site->name,
            'url' => $site->url,
            'address' => $site->address,
            'thumbnail' => 'data:image/jpeg;base64,same',
            'description' => $site->description,
            'price_min' => $site->price_min,
            'price_max' => $site->price_max,
        ], $user)
        ->assertOk();

        $site = $site->fresh();
        $this->assertEquals($image->id, $site->thumbnail_id);

        $imageService = new ImageService();
        $base64 = $imageService->encode($site->thumbnail);
        $this->assertEquals('data:image/jpeg;base64,same', $base64);
        $this->assertEquals($image_updated_at, $site->thumbnail->updated_at);
    }

    public function testPositive_update_site_updates_existing_thumbnail_when_value_differs()
    {
        $imageService = new ImageService();
        $user = User::factory()->create();
        $image = Image::factory()->create($imageService->decode('data:image/jpeg;base64,old'));
        $site = Site::factory()->create([
            'user_id' => $user->id,
            'thumbnail_id' => $image->id,
        ]);

        $this->putAuthApi("/api/user/sites/{$site->id}", [
            'name' => $site->name,
            'url' => $site->url,
            'address' => $site->address,
            'thumbnail' => 'data:image/png;base64,update',
            'description' => $site->description,
            'price_min' => $site->price_min,
            'price_max' => $site->price_max,
        ], $user)
        ->assertOk();

        $site = $site->fresh();
        $this->assertEquals($image->id, $site->thumbnail_id);

        $imageService = new ImageService();
        $base64 = $imageService->encode($site->thumbnail);
        $this->assertEquals('data:image/png;base64', explode(',', $base64, 2)[0]);
    }

    public function testPositive_delete_user_sites_soft_deletes_record()
    {
        $user = User::factory()->create();
        $image = Image::factory()->create();
        $site = Site::factory()->create([
            'user_id' => $user->id,
            'thumbnail_id' => $image->id,
        ]);

        $this->deleteAuthApi("/api/user/sites/{$site->id}", [], $user)
            ->assertOk();

        $this->assertSoftDeleted('sites', ['id' => $site->id]);
        $this->assertSoftDeleted('images', ['id' => $image->id]);
    }

    public function testNegative_put_and_delete_other_users_site()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $other->id]);

        $this->putAuthApi("/api/user/sites/{$site->id}", [
            'name' => '不正更新',
            'url' => 'https://invalid.com',
            'address' => '不正住所',
            'thumbnail' => null,
            'description' => '不正',
            'price_min' => 1000,
            'price_max' => 2000,
        ], $user)
        ->assertForbidden();

        $this->deleteAuthApi("/api/user/sites/{$site->id}", [], $user)
            ->assertForbidden();
    }
}
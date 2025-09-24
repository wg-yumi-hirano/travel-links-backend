<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\User;

class SiteSeeder extends Seeder
{
    public static User $user;

    public function run(): void
    {
        Site::factory()
            ->count(20)
            ->for(self::$user)
            ->create();
    }
}
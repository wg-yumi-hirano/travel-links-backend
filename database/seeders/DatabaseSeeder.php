<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'email' => 'sample@example.com',
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Hash::make('samplepassword'),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);
        SiteSeeder::$user = $user;

        $this->call(SiteSeeder::class);
    }
}

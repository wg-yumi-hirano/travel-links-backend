<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Site;

class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'url' => 'https://google.com',
            'address' => '東京都' . $this->faker->words(10, true),
            'thumbnail' => $this->faker->imageUrl(),
            'description' => $this->faker->paragraphs(2, true),
            'price_max' => $this->faker->numberBetween(10000, 30000),
            'price_min' => $this->faker->numberBetween(5000, 10000),
        ];
    }
}
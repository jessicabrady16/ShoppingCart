<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Bezhanov\Faker\Provider\Commerce as CommerceProvider;

class ProductFactory extends Factory
{
  protected $model = Product::class;

  public function definition(): array
  {
    // enable Commerce provider for productName()
    if (!method_exists($this->faker, 'productName')) {
      $this->faker->addProvider(new CommerceProvider($this->faker));
    }

    $name  = $this->faker->productName();              // ≈ faker.commerce.product()
    $price = $this->faker->randomFloat(2, 0.5, 99.99);
    $seed  = $this->faker->unique()->numberBetween(1, 1_000_000);

    return [
      'name'      => $name,
      'price'     => $price,
      'image_url' => "https://picsum.photos/seed/{$seed}/600/400",
    ];
  }
}

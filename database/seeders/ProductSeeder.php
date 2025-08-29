<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  public function run(): void
  {
    \App\Models\Product::truncate();
    $products = \App\Models\Product::factory()->count(12)->create();

    foreach ($products as $p) {
      dispatch(new \App\Jobs\GenerateProductImage($p->id));
    }
  }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  public function run(): void
  {
    Product::truncate();             // reset for demo/dev
    Product::factory()->count(12)->create();
  }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
  public function run(): void
  {
    $items = [
      ['name' => 'Notebook', 'price' => 3.50],
      ['name' => 'Pencil',   'price' => 1.25],
      ['name' => 'Eraser',   'price' => 0.99],
      ['name' => 'Ruler',    'price' => 2.50],
      ['name' => 'Backpack', 'price' => 24.00],
    ];
    foreach ($items as $i) {
      Product::updateOrCreate(['name' => $i['name']], $i);
    }
  }
}

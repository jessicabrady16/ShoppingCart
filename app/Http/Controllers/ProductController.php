<?php

namespace App\Http\Controllers;

use App\Models\Product;

final class ProductController extends Controller
{
  public function index()
  {
    // Cast price to float for the front end
    $list = Product::query()->orderBy('id')->get()->map(fn($p) => [
      'id'    => (int) $p->id,
      'name'  => $p->name,
      'price' => (float) $p->price,
    ])->values();

    return response()->json(['products' => $list]);
  }
}

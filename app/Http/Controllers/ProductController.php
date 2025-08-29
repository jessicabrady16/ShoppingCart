<?php

namespace App\Http\Controllers;

use App\Models\Product;

final class ProductController extends Controller
{
  public function index()
  {
    $list = \App\Models\Product::query()->orderBy('id')->get()
      ->map(fn($p) => [
        'id'        => (int) $p->id,
        'name'      => $p->name,
        'price'     => (float) $p->price,
        'image_url' => $p->image_url ?: "https://picsum.photos/seed/{$p->id}/600/400",
      ])->values();

    return response()->json(['products' => $list]);
  }
}

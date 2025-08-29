<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

final class CartController extends Controller
{
  public function __construct(private CartService $cart) {}

  public function show(Request $request)
  {
    $taxRate  = (float) $request->query('tax_rate', 0.0);
    $discount = (float) $request->query('discount', 0.0);
    return response()->json($this->cart->get($taxRate, $discount));
  }

  public function store(StoreCartItemRequest $request)
  {
    $d = $request->validated();
    $payload = $this->cart->add((int)$d['product_id'], (string)$d['name'], (float)$d['price'], (int)$d['quantity']);
    return response()->json($payload, 201);
  }

  public function update(UpdateCartItemRequest $request, int $productId)
  {
    $qty = (int) $request->validated()['quantity'];
    $payload = $this->cart->update($productId, $qty);
    return response()->json($payload);
  }

  public function destroy(int $productId)
  {
    $payload = $this->cart->remove($productId);
    return response()->json($payload, 200);
  }

  public function clear()
  {
    return response()->json($this->cart->clear());
  }
}

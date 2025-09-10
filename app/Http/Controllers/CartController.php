<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CartService;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;

final class CartController extends Controller
{
  public function __construct(private CartService $cart) {}

  /**
   * GET /api/cart?tax_rate=&discount=&discount_type=flat|percent
   */
  public function show(Request $request): JsonResponse
  {
    return response()->json($this->cartResponse($request));
  }

  /**
   * POST /api/cart/items
   * body: { product_id, name, price, quantity }
   */
  public function store(StoreCartItemRequest $request): JsonResponse
  {
    $v = $request->validated();

    // CartService::add(int $productId, string $name, float $price, int $quantity)
    $this->cart->add(
      (int)   $v['product_id'],
      (string)$v['name'],
      (float) $v['price'],
      (int)   $v['quantity']
    );

    return response()->json($this->cartResponse($request), 201);
  }

  /**
   * PATCH /api/cart/items/{productId}
   * body: { quantity }
   * quantity=0 removes the item
   */
  public function update(UpdateCartItemRequest $request, int $productId): JsonResponse
  {
    $v = $request->validated();
    $this->cart->update((int)$productId, (int)$v['quantity']);

    return response()->json($this->cartResponse($request));
  }

  /**
   * DELETE /api/cart/items/{productId}
   */
  public function destroy(Request $request, int $productId): JsonResponse
  {
    $this->cart->remove((int)$productId);

    return response()->json($this->cartResponse($request));
  }

  /**
   * DELETE /api/cart
   */
  public function clear(Request $request): JsonResponse
  {
    $this->cart->clear();

    return response()->json($this->cartResponse($request));
  }

  /**
   * Centralized response w/ admin gating + percent/flat discounts.
   */
  private function cartResponse(Request $request): array
  {
    $role = (string)$request->session()->get('role', 'shopper');

    // shopper cannot influence pricing
    if ($role !== 'admin') {
      return $this->cart->get(0.0, 0.0);
    }

    // admin-only knobs
    $taxRate       = (float)$request->query('tax_rate', 0.0);
    $discountInput = (float)$request->query('discount', 0.0);
    $type          = (string)$request->query('discount_type', 'flat'); // flat|percent

    // Convert percent -> flat dollars using current subtotal (no tax/discount)
    $discountFlat = $discountInput;
    if ($type === 'percent') {
      // clamp 0–100
      $pct = max(0.0, min(100.0, $discountInput));
      $snap = $this->cart->get(0.0, 0.0);
      $subtotal = (float)($snap['subtotal'] ?? 0.0);
      $discountFlat = round($subtotal * ($pct / 100.0), 2);
    }

    return $this->cart->get($taxRate, $discountFlat);
  }
}

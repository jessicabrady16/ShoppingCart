<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Cart\{Cart, CartInterface, Product};
use Illuminate\Contracts\Session\Session;

final class CartService
{
  private const KEY = 'cart';

  public function __construct(private Session $session) {}

  private function load(): CartInterface
  {
    $data = $this->session->get(self::KEY);
    return is_array($data) ? Cart::fromStorageArray($data) : new Cart();
  }

  private function save(CartInterface $cart): void
  {
    $this->session->put(self::KEY, $cart->toStorageArray());
  }

  public function get(float $taxRate = 0.0, float $discount = 0.0): array
  {
    return $this->load()->toArrayForApi($taxRate, $discount);
  }

  public function add(int $productId, string $name, float $price, int $quantity): array
  {
    $cart = $this->load();
    $cart->addItem(new Product($productId, $name, $price), $quantity);
    $this->save($cart);
    return $cart->toArrayForApi();
  }

  public function update(int $productId, int $quantity): array
  {
    $cart = $this->load();
    $cart->updateQuantity($productId, $quantity);
    $this->save($cart);
    return $cart->toArrayForApi();
  }

  public function remove(int $productId): array
  {
    $cart = $this->load();
    $cart->removeItem($productId);
    $this->save($cart);
    return $cart->toArrayForApi();
  }

  public function clear(): array
  {
    $this->session->forget(self::KEY);
    return (new Cart())->toArrayForApi();
  }

  public function getWithPricing(float $taxRate = 0.0, float $discount = 0.0, string $discountType = 'flat'): array
  {
    return $this->load()->toArrayForApi($taxRate, $discount, $discountType);
  }
}

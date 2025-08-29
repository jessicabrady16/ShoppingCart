<?php

declare(strict_types=1);

namespace App\Domain\Cart;

interface CartInterface
{
  public function addItem(Product $product, int $qty): void;
  public function updateQuantity(int $productId, int $qty): void;
  public function removeItem(int $productId): void;
  /** @return CartItem[] */
  public function items(): array;
  public function subTotal(): float;
  public function total(float $taxRate = 0.0, float $discount = 0.0): float;
  public function checkoutUrl(): string;
  public function toArrayForApi(float $taxRate = 0.0, float $discount = 0.0): array;

  /** Storage helpers */
  public function toStorageArray(): array;
  public static function fromStorageArray(array $data): self;
}

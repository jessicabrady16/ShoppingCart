<?php

declare(strict_types=1);

namespace App\Domain\Cart;

final class Product
{
  public function __construct(
    public readonly int $id,
    public readonly string $name,
    public readonly float $price
  ) {
    if ($this->price <= 0) throw new \InvalidArgumentException('Price must be > 0');
  }
}

<?php

declare(strict_types=1);

namespace App\Domain\Cart;

final class CartItem implements \JsonSerializable
{
  public function __construct(
    public Product $product,
    public int $qty
  ) {
    if ($this->qty <= 0) throw new \InvalidArgumentException('Quantity must be > 0');
  }

  public function lineTotal(): float
  {
    return round($this->product->price * $this->qty, 2);
  }

  public function jsonSerialize(): array
  {
    return [
      'product'   => [
        'id'    => $this->product->id,
        'name'  => $this->product->name,
        'price' => $this->product->price,
      ],
      'quantity'  => $this->qty,
      'lineTotal' => $this->lineTotal(),
    ];
  }
}

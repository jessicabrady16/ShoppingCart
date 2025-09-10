<?php

declare(strict_types=1);

namespace App\Domain\Cart;

final class Cart implements CartInterface
{
  /** @var array<int,CartItem> keyed by product id */
  private array $items = [];

  public function addItem(Product $product, int $qty): void
  {
    if ($qty <= 0) throw new \InvalidArgumentException('Quantity must be > 0');

    $existing = $this->items[$product->id] ?? null;
    if ($existing) {
      $existing->qty += $qty;
      return;
    }
    $this->items[$product->id] = new CartItem($product, $qty);
  }

  public function updateQuantity(int $productId, int $qty): void
  {
    if (!isset($this->items[$productId])) return;
    if ($qty <= 0) {
      $this->removeItem($productId);
    } else {
      $this->items[$productId]->qty = $qty;
    }
  }

  public function removeItem(int $productId): void
  {
    unset($this->items[$productId]);
  }

  /** @return CartItem[] */
  public function items(): array
  {
    return array_values($this->items);
  }

  public function checkoutUrl(): string
  {
    return 'https://example.com/checkout';
  }

  public function subTotal(): float
  {
    $sum = 0.0;
    foreach ($this->items as $item) $sum += $item->lineTotal();
    return round($sum, 2);
  }

  public function total(float $taxRate = 0.0, float $discount = 0.0, string $discountType = 'flat'): float
  {
    return $this->totalsBreakdown($taxRate, $discount, $discountType)['total'];
  }


  /** Single source of truth for money math. */
  private function totalsBreakdown(float $taxRate, float $discount, string $discountType = 'flat'): array
  {
    if ($taxRate < 0.0)  throw new \InvalidArgumentException('Tax rate must be >= 0');
    if ($discount < 0.0) throw new \InvalidArgumentException('Discount must be >= 0');

    $subtotal = $this->subTotal();

    $type = strtolower($discountType);
    if (!in_array($type, ['none', 'flat', 'percent'], true)) {
      throw new \InvalidArgumentException('Discount type must be none|flat|percent');
    }

    $discountAmount = 0.0;
    if ($type === 'flat') {
      $discountAmount = $discount;
    } elseif ($type === 'percent') {
      // cap percent to [0..100]
      $pct = max(0.0, min(100.0, $discount));
      $discountAmount = round($subtotal * ($pct / 100.0), 2);
    }

    $afterDiscount = round(max(0.0, $subtotal - $discountAmount), 2);
    $tax           = round($afterDiscount * $taxRate, 2);
    $total         = round($afterDiscount + $tax, 2);

    return [
      'subtotal'        => $subtotal,
      'discount_type'   => $type,
      'discount'        => $discount,       // as supplied (flat $ or percent %)
      'discount_amount' => $discountAmount, // actual dollars taken off
      'taxRate'         => $taxRate,
      'tax'             => $tax,
      'total'           => $total,
    ];
  }


  /** Shape of API payload. */
  public function toArrayForApi(float $taxRate = 0.0, float $discount = 0.0, string $discountType = 'flat'): array
  {
    $t = $this->totalsBreakdown($taxRate, $discount, $discountType);

    return [
      'items'           => array_map(fn($i) => $i->jsonSerialize(), $this->items()),
      'subtotal'        => $t['subtotal'],
      'discount_type'   => $t['discount_type'],
      'discount'        => $t['discount'],
      'discount_amount' => $t['discount_amount'],
      'tax_rate'        => $t['taxRate'],
      'tax'             => $t['tax'],
      'total'           => $t['total'],
      'checkout'        => $this->checkoutUrl(),
    ];
  }

  /** Persist minimal data in session */
  public function toStorageArray(): array
  {
    return [
      'items' => array_map(function (CartItem $i) {
        return [
          'product' => [
            'id'    => $i->product->id,
            'name'  => $i->product->name,
            'price' => $i->product->price,
          ],
          'qty' => $i->qty,
        ];
      }, $this->items()),
    ];
  }

  public static function fromStorageArray(array $data): self
  {
    $cart = new self();
    foreach (($data['items'] ?? []) as $row) {
      $p = $row['product'];
      $cart->addItem(new Product((int)$p['id'], (string)$p['name'], (float)$p['price']), (int) $row['qty']);
    }
    return $cart;
  }
}

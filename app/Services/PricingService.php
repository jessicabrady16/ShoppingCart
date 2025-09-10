<?php

namespace App\Services;

class PricingService
{
  private const KEY = 'pricing';

  public function get(): array
  {
    return cache()->remember(self::KEY, 3600, function () {
      return [
        'tax_rate'       => (float) config('cart.default_tax_rate'),
        'discount_type'  => (string) config('cart.discount.type', 'none'),
        'discount'       => (float) config('cart.discount.amount', 0.0),
      ];
    });
  }

  public function update(array $data): array
  {
    $cur = $this->get();
    $merged = array_merge($cur, $data);
    cache()->put(self::KEY, $merged, 86400);
    return $merged;
  }
}

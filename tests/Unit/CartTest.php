<?php

use App\Domain\Cart\{Cart, Product};

it('adds items and computes totals with discount and tax', function () {
  $c = new Cart();
  $c->addItem(new Product(1, 'Notebook', 3.50), 2);
  $c->addItem(new Product(2, 'Pencil', 1.25), 3);
  expect($c->subTotal())->toBe(10.75);
  expect($c->total(0.0725, 0.30))->toBe(11.21);
});

it('removes when quantity updated to zero', function () {
  $c = new Cart();
  $c->addItem(new Product(1, 'A', 2.00), 1);
  $c->updateQuantity(1, 0);
  expect($c->items())->toHaveCount(0);
});

it('serializes and restores from storage array', function () {
  $c = new Cart();
  $c->addItem(new Product(1, 'A', 2.00), 3);
  $snap = $c->toStorageArray();
  $restored = Cart::fromStorageArray($snap);
  expect($restored->subTotal())->toBe($c->subTotal());
});

it('guards invalid inputs', function () {
  $c = new Cart();
  expect(fn() => $c->addItem(new Product(1, 'A', 1.00), 0))
    ->toThrow(InvalidArgumentException::class);
  expect(fn() => new Product(2, 'B', 0.00))
    ->toThrow(InvalidArgumentException::class);
});

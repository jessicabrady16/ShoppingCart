<?php

use Illuminate\Testing\Fluent\AssertableJson as AJ;

it('returns empty cart by default', function () {
  $this->getJson('/api/cart')
    ->assertOk()
    ->assertJson(
      fn(AJ $json) => $json
        ->where('items', [])
        ->where('subtotal', 0)    // 0 not 0.0
        ->where('total', 0)
        ->etc()
    );
});

it('can add, update, and remove with session persistence', function () {
  $this->postJson('/api/cart/items', [
    'product_id' => 42,
    'name'       => 'Notebook',
    'price'      => 3.50,
    'quantity'   => 2,
  ])->assertCreated();

  $this->getJson('/api/cart')
    ->assertOk()
    ->assertJson(fn(AJ $json) => $json->where('subtotal', 7)->etc()); // 7, not 7.00

  $this->patchJson('/api/cart/items/42', ['quantity' => 3])->assertOk();

  $this->getJson('/api/cart')
    ->assertOk()
    ->assertJson(fn(AJ $json) => $json->where('subtotal', 10.5)->etc()); // 10.5, not 10.50

  $this->deleteJson('/api/cart/items/42')->assertOk();

  $this->getJson('/api/cart')
    ->assertOk()
    ->assertJson(fn(AJ $json) => $json->where('subtotal', 0)->etc());
});

it('applies discount before tax when reading the cart', function () {
  $this->postJson('/api/cart/items', [
    'product_id' => 1,
    'name' => 'A',
    'price' => 5.00,
    'quantity' => 2,
  ])->assertCreated();

  $this->getJson('/api/cart?discount=3.00&tax_rate=0.1')
    ->assertOk()
    ->assertJson(
      fn(AJ $json) => $json
        ->where('subtotal', 10)   // 10 not 10.00
        ->where('discount', 3)
        ->where('tax_rate', 0.1)
        ->where('tax', 0.7)
        ->where('total', 7.7)
        ->etc()
    );
});

it('validates store and update requests', function () {
  $this->postJson('/api/cart/items', ['product_id' => 1, 'name' => 'A', 'price' => 0, 'quantity' => 1])
    ->assertStatus(422);

  $this->postJson('/api/cart/items', ['product_id' => 1, 'name' => 'A', 'price' => 1.00, 'quantity' => 0])
    ->assertStatus(422);

  $this->patchJson('/api/cart/items/1', ['quantity' => -1])
    ->assertStatus(422);
});

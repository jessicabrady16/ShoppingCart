<script setup>
import { ref, onMounted, computed } from 'vue';

const taxRate = ref(0.0725);
const discount = ref(0.00);
const cart = ref({ items: [], subtotal: 0, tax: 0, total: 0 });

const form = ref({ product_id: 1, name: 'Notebook', price: 3.5, quantity: 1 });

async function fetchCart() {
  const q = new URLSearchParams({ tax_rate: String(taxRate.value), discount: String(discount.value) });
  const res = await fetch(`/api/cart?${q.toString()}`);
  cart.value = await res.json();
}

async function addItem() {
  await fetch('/api/cart/items', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(form.value),
  });
  await fetchCart();
}

async function updateQty(productId, qty) {
  await fetch(`/api/cart/items/${productId}`, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ quantity: qty }),
  });
  await fetchCart();
}

async function removeItem(productId) {
  await fetch(`/api/cart/items/${productId}`, { method: 'DELETE' });
  await fetchCart();
}

async function clearCart() {
  await fetch('/api/cart', { method: 'DELETE' });
  await fetchCart();
}

onMounted(fetchCart);

const hasItems = computed(() => (cart.value.items ?? []).length > 0);
</script>

<template>
  <main style="max-width: 800px; margin: 2rem auto; font-family: ui-sans-serif, system-ui;">
    <h1>Shopping Cart</h1>

    <section style="display: grid; gap: .5rem; grid-template-columns: repeat(4, 1fr); align-items: end;">
      <label>Product ID <input type="number" v-model.number="form.product_id" min="1" /></label>
      <label>Name <input type="text" v-model="form.name" /></label>
      <label>Price <input type="number" step="0.01" min="0.01" v-model.number="form.price" /></label>
      <label>Qty <input type="number" min="1" v-model.number="form.quantity" /></label>
      <button @click="addItem">Add to cart</button>
    </section>

    <section style="margin-top:1rem; display:flex; gap:1rem; align-items:center;">
      <label>Tax Rate <input type="number" step="0.0001" v-model.number="taxRate" @change="fetchCart" /></label>
      <label>Discount <input type="number" step="0.01" min="0" v-model.number="discount" @change="fetchCart" /></label>
      <button @click="clearCart">Clear</button>
      <button @click="fetchCart">Refresh</button>
    </section>

    <section v-if="hasItems" style="margin-top:1rem;">
      <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th style="text-align:left;">Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Line Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in cart.items" :key="row.product.id">
            <td>{{ row.product.name }} (#{{ row.product.id }})</td>
            <td style="text-align:right;">{{ row.product.price.toFixed(2) }}</td>
            <td style="text-align:center;">
              <input type="number" min="0" :value="row.quantity"
                @change="e => updateQty(row.product.id, Number(e.target.value))" />
            </td>
            <td style="text-align:right;">{{ row.lineTotal.toFixed(2) }}</td>
            <td><button @click="removeItem(row.product.id)">Remove</button></td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top:1rem; text-align:right;">
        <div>Subtotal: <strong>{{ cart.subtotal.toFixed(2) }}</strong></div>
        <div>Tax: <strong>{{ cart.tax.toFixed(2) }}</strong></div>
        <div>Total: <strong>{{ cart.total.toFixed(2) }}</strong></div>
      </div>
    </section>

    <p v-else style="margin-top:1rem;">Cart is empty.</p>
  </main>
</template>

<style scoped>
label {
  display: flex;
  flex-direction: column;
  font-size: .9rem;
}

input {
  padding: .4rem;
}

button {
  padding: .5rem .8rem;
}
</style>

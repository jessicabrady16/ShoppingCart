<script setup>
import { ref, computed, onMounted } from 'vue'

const taxRate = ref(0.0725)
const discount = ref(0.00)
const cart = ref({ items: [], subtotal: 0, tax: 0, total: 0, discount: 0, tax_rate: 0 })

const form = ref({ product_id: 1, name: 'Notebook', price: 3.50, quantity: 1 })
const busy = ref(false)
const err = ref('')

const fmt = (n) => Number(n ?? 0).toFixed(2)

async function fetchCart() {
  err.value = ''
  try {
    const q = new URLSearchParams({ tax_rate: String(taxRate.value), discount: String(discount.value) })
    const res = await fetch(`/api/cart?${q}`)
    cart.value = await res.json()
  } catch (e) { err.value = String(e) }
}

async function addItem() {
  err.value = ''; busy.value = true
  try {
    await fetch('/api/cart/items', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form.value),
    })
    await fetchCart()
  } catch (e) { err.value = String(e) } finally { busy.value = false }
}

async function updateQty(productId, qty) {
  err.value = ''; busy.value = true
  try {
    await fetch(`/api/cart/items/${productId}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ quantity: Number(qty) }),
    })
    await fetchCart()
  } catch (e) { err.value = String(e) } finally { busy.value = false }
}

async function removeItem(productId) {
  err.value = ''; busy.value = true
  try {
    await fetch(`/api/cart/items/${productId}`, { method: 'DELETE' })
    await fetchCart()
  } catch (e) { err.value = String(e) } finally { busy.value = false }
}

async function clearCart() {
  err.value = ''; busy.value = true
  try {
    await fetch('/api/cart', { method: 'DELETE' })
    await fetchCart()
  } catch (e) { err.value = String(e) } finally { busy.value = false }
}

onMounted(fetchCart)
const hasItems = computed(() => (cart.value.items ?? []).length > 0)
</script>

<template>
  <main style="max-width: 900px; margin: 2rem auto; font-family: ui-sans-serif, system-ui;">
    <h1 style="display:flex; gap:.5rem; align-items:center">
      Shopping Cart <small style="font-weight:400;color:#666">(Vue + API)</small>
    </h1>

    <section style="display:grid; gap:.5rem; grid-template-columns: repeat(5, 1fr); align-items:end; margin:.75rem 0;">
      <label>Product ID <input type="number" min="1" v-model.number="form.product_id" /></label>
      <label>Name <input type="text" v-model="form.name" /></label>
      <label>Price <input type="number" min="0.01" step="0.01" v-model.number="form.price" /></label>
      <label>Qty <input type="number" min="1" v-model.number="form.quantity" /></label>
      <button :disabled="busy" @click="addItem">Add</button>
    </section>

    <section style="display:flex; gap:1rem; align-items:center; margin:.5rem 0;">
      <label>Tax Rate <input type="number" step="0.0001" v-model.number="taxRate" @change="fetchCart" /></label>
      <label>Discount <input type="number" step="0.01" min="0" v-model.number="discount" @change="fetchCart" /></label>
      <button :disabled="busy" @click="clearCart">Clear</button>
      <button :disabled="busy" @click="fetchCart">Refresh</button>
      <span v-if="busy">Working…</span>
    </section>

    <p v-if="err" style="color:#b00;">{{ err }}</p>

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
            <td style="text-align:right;">{{ fmt(row.product.price) }}</td>
            <td style="text-align:center;">
              <input type="number" min="0" :value="row.quantity"
                     @change="e => updateQty(row.product.id, e.target.value)" />
            </td>
            <td style="text-align:right;">{{ fmt(row.lineTotal) }}</td>
            <td><button :disabled="busy" @click="removeItem(row.product.id)">Remove</button></td>
          </tr>
        </tbody>
      </table>

      <div style="margin-top:1rem; text-align:right;">
        <div>Subtotal: <strong>{{ fmt(cart.subtotal) }}</strong></div>
        <div>Tax: <strong>{{ fmt(cart.tax) }}</strong></div>
        <div>Total: <strong>{{ fmt(cart.total) }}</strong></div>
      </div>
    </section>

    <p v-else style="margin-top:1rem;">Cart is empty.</p>
  </main>
</template>

<style scoped>
label { display:flex; flex-direction:column; gap:.25rem; font-size:.95rem; }
input { padding:.4rem; }
button { padding:.5rem .8rem; cursor:pointer; }
button[disabled]{ opacity:.6; cursor:not-allowed; }
</style>

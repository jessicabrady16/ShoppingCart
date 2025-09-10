<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import AppHeader from './AppHeader.vue'

type Role = 'admin' | 'shopper' | null
type Product = { id: number; name: string; price: number; image_url?: string }
type CartItem = { product: Product; quantity: number; lineTotal: number }
type CartRes = {
  items: CartItem[]
  subtotal: number
  discount: number
  tax_rate: number
  tax: number
  total: number
  checkout: string
}

const meRole = ref < Role > (null)
const isAdmin = computed(() => meRole.value === 'admin')

// admin-only inputs
const discount = ref < number > (0)
const taxRate = ref < number > (0)

// cart state
const cart = ref < CartRes | null > (null)
const busy = ref(false)
const err = ref('')

async function fetchMe() {
  const res = await fetch('/api/auth/me', { credentials: 'same-origin' })
  meRole.value = res.ok ? (await res.json()).role as Role : null
}

const effectiveDiscount = computed(() => (isAdmin.value ? discount.value : 0))
const effectiveTaxRate = computed(() => (isAdmin.value ? taxRate.value : 0))

async function loadCart() {
  try {
    busy.value = true
    err.value = ''
    const qs = new URLSearchParams({
      discount: String(effectiveDiscount.value || 0),
      tax_rate: String(effectiveTaxRate.value || 0),
    })
    const res = await fetch('/api/cart?' + qs.toString(), { credentials: 'same-origin' })
    cart.value = await res.json()
  } catch (e) {
    err.value = String(e)
  } finally {
    busy.value = false
  }
}

watch([effectiveDiscount, effectiveTaxRate], () => {
  // Only refetch automatically when admin changes pricing inputs
  if (isAdmin.value) loadCart()
})

onMounted(async () => {
  await fetchMe()
  await loadCart()
})
</script>

<template>
  <main style="max-width: 960px; margin: 2rem auto; font-family: ui-sans-serif, system-ui;">
    <AppHeader title="Cart" />

    <p v-if="err" style="color:#b00">{{ err }}</p>

    <!-- Admin-only pricing controls -->
    <section v-if="isAdmin" style="display:flex; gap:1rem; align-items:flex-end; margin:.5rem 0 1rem;">
      <label>
        <div>Flat discount ($)</div>
        <input type="number" min="0" step="0.01" v-model.number="discount" style="padding:.4rem; width: 10rem;" />
      </label>

      <label>
        <div>Tax rate</div>
        <input type="number" min="0" step="0.0001" v-model.number="taxRate" style="padding:.4rem; width: 8rem;" />
      </label>

      <button :disabled="busy" @click="loadCart">Apply</button>
    </section>

    <section v-else style="margin:.5rem 0 1rem; color:#555;">
      Prices shown are without admin discounts/tax overrides.
    </section>

    <div v-if="cart">
      <ul style="list-style:none; padding:0; margin:0 0 1rem 0;">
        <li v-for="i in cart.items" :key="i.product.id"
          style="display:flex;gap:1rem;align-items:center;border-bottom:1px solid #eee;padding:.5rem 0;">
          <img :src="i.product.image_url" :alt="i.product.name"
            style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
          <div style="flex:1">
            <div style="font-weight:600">{{ i.product.name }}</div>
            <div style="color:#666">${{ i.product.price.toFixed(2) }} × {{ i.quantity }}</div>
          </div>
          <div style="width:100px;text-align:right">${{ i.lineTotal.toFixed(2) }}</div>
        </li>
      </ul>

      <div style="text-align:right">
        <div>Subtotal: ${{ cart.subtotal.toFixed(2) }}</div>
        <div v-if="cart.discount">Discount: -${{ cart.discount.toFixed(2) }}</div>
        <div v-if="cart.tax">Tax ({{ cart.tax_rate }}): ${{ cart.tax.toFixed(2) }}</div>
        <div style="font-weight:700; font-size:1.2rem;">Total: ${{ cart.total.toFixed(2) }}</div>
      </div>
    </div>
  </main>
</template>

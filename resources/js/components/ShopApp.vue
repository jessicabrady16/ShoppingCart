<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppHeader from './AppHeader.vue'

type Product = {
  id: number
  name: string
  price: number
  image_url: string | null
}

type ProductsResponse = {
  products: Product[]
}

const products = ref < Product[] > ([])
const qty = ref < Record < number, number>> ({})
const busy = ref < boolean > (false)
const err = ref < string > ('')

async function load(): Promise<void> {
  try {
    const res = await fetch('/api/products')
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const data = (await res.json()) as Partial<ProductsResponse>
    products.value = Array.isArray(data.products) ? data.products : []
    for (const p of products.value) {
      if (qty.value[p.id] == null) qty.value[p.id] = 1
    }
  } catch (e) {
    err.value = e instanceof Error ? e.message : String(e)
  }
}

async function addToCart(p: Product): Promise<void> {
  err.value = ''
  busy.value = true
  try {
    const body = {
      product_id: p.id,
      name: p.name,
      price: p.price,
      quantity: Number(qty.value[p.id] ?? 1),
    }
    const res = await fetch('/api/cart/items', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    })
    if (!res.ok) {
      const text = await res.text()
      throw new Error(`HTTP ${res.status}: ${text}`)
    }
  } catch (e) {
    err.value = e instanceof Error ? e.message : String(e)
  } finally {
    busy.value = false
  }
}

onMounted(load)
</script>

<template>
  <main style="max-width: 960px; margin: 2rem auto; font-family: ui-sans-serif, system-ui;">
    <AppHeader title="Shop" />

    <p v-if="err" style="color:#b00">{{ err }}</p>

    <section
      style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); margin-top:1rem;">
      <article v-for="p in products" :key="p.id" style="border:1px solid #ddd;border-radius:12px;padding:1rem;">
        <img :src="p.image_url ?? ''" :alt="p.name"
          style="width:100%;height:140px;object-fit:cover;border-radius:8px;margin-bottom:.5rem;" loading="lazy" />
        <h3 style="margin:.2rem 0">{{ p.name }}</h3>
        <p style="color:#555;margin:.2rem 0">$ {{ Number(p.price).toFixed(2) }}</p>
        <div style="display:flex;gap:.5rem;align-items:center;">
          <input type="number" min="1" v-model.number="qty[p.id]" style="width:80px;padding:.4rem;" />
          <button :disabled="busy" @click="addToCart(p)" style="padding:.5rem .8rem;cursor:pointer;">
            Add to Cart
          </button>
        </div>
      </article>
    </section>
  </main>
</template>

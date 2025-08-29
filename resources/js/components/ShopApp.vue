<script setup>
import { ref, onMounted } from 'vue'

const products = ref([])
const qty = ref({})
const busy = ref(false)
const err = ref('')

const load = async () => {
  try {
    const res = await fetch('/api/products')
    const data = await res.json()
    products.value = data.products ?? []
    for (const p of products.value) if (!(p.id in qty.value)) qty.value[p.id] = 1
  } catch (e) { err.value = String(e) }
}

const addToCart = async (p) => {
  err.value = ''; busy.value = true
  try {
    await fetch('/api/cart/items', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        product_id: p.id,
        name: p.name,
        price: p.price,
        quantity: Number(qty.value[p.id] ?? 1),
      }),
    })
  } catch (e) { err.value = String(e) }
  finally { busy.value = false }
}

onMounted(load)
</script>

<template>
  <main style="max-width: 960px; margin: 2rem auto; font-family: ui-sans-serif, system-ui;">
    <header style="display:flex; justify-content:space-between; align-items:center;">
      <h1>Shop</h1>
      <nav style="display:flex; gap:.75rem;">
        <a href="/">Home</a>
        <a href="/cart">Cart</a>
        <a href="/api-docs">API Docs</a>
      </nav>
    </header>

    <p v-if="err" style="color:#b00">{{ err }}</p>

    <section
      style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); margin-top:1rem;">
      <article v-for="p in products" :key="p.id" style="border:1px solid #ddd; border-radius:12px; padding:1rem;">
        <h3 style="margin:.2rem 0">{{ p.name }}</h3>
        <p style="color:#555; margin:.2rem 0">$ {{ Number(p.price).toFixed(2) }}</p>
        <div style="display:flex; gap:.5rem; align-items:center;">
          <input type="number" min="1" v-model.number="qty[p.id]" style="width:80px; padding:.4rem;" />
          <button :disabled="busy" @click="addToCart(p)" style="padding:.5rem .8rem; cursor:pointer;">Add to
            Cart</button>
        </div>
      </article>
    </section>
  </main>
</template>

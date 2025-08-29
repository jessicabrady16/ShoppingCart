<script setup lang="ts">
const props = defineProps({ title: { type: String, default: 'ShoppingCart' } })
const isActive = (href) => window.location.pathname === href
</script>

<template>
  <header
    style="display:flex;justify-content:space-between;align-items:center;margin:2rem auto 1rem;max-width:960px;font-family:ui-sans-serif,system-ui;">
    <h1 style="margin:0">{{ title }}</h1>
    <nav style="display:flex;gap:.75rem;">
      <a :href="'/'" :style="isActive('/') ? 'font-weight:700;text-decoration:underline' : ''">Home</a>
      <a :href="'/shop'" :style="isActive('/shop') ? 'font-weight:700;text-decoration:underline' : ''">Shop</a>
      <a :href="'/cart'" :style="isActive('/cart') ? 'font-weight:700;text-decoration:underline' : ''">Cart</a>
      <a :href="'/api-docs'" :style="isActive('/api-docs') ? 'font-weight:700;text-decoration:underline' : ''">API
        Docs</a>
    </nav>
  </header>
</template>

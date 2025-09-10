<script setup lang="ts">
import { ref, onMounted } from 'vue'

defineProps<{ title?: string }>()

type Role = 'admin' | 'shopper' | null
type Me = { email: string | null; role: Role }

const me = ref<Me>({ email: null, role: null })
const busy = ref(false)
const err = ref('')

const fetchMe = async () => {
  try {
    const res = await fetch('/api/auth/me', { credentials: 'same-origin' })
    me.value = res.ok ? await res.json() : { email: null, role: null }
  } catch (e) {
    err.value = String(e)
  }
}

const login = async (role: 'admin' | 'shopper') => {
  busy.value = true; err.value = ''
  try {
    await fetch('/api/auth/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ email: `${role}@example.test`, role }),
    })
    await fetchMe()
  } finally {
    busy.value = false
  }
}

const logout = async () => {
  busy.value = true
  try {
    await fetch('/api/auth/logout', { method: 'POST', credentials: 'same-origin' })
    await fetchMe()
  } finally {
    busy.value = false
  }
}

onMounted(fetchMe)
</script>

<template>
  <header style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;">
    <nav style="display:flex; gap:.75rem;">
      <a href="/">Home</a>
      <a href="/cart">Cart</a>
      <a href="/shop">Shop</a>
      <a href="/api-docs">API Docs</a>
    </nav>

    <div style="display:flex;align-items:center;gap:.5rem;">
      <span v-if="me.role === 'admin'"
        style="padding:.2rem .5rem;border-radius:999px;background:#DCFCE7;border:1px solid #16A34A;color:#166534;font-size:.85rem;">
        ADMIN
      </span>
      <span v-else-if="me.role === 'shopper'"
        style="padding:.2rem .5rem;border-radius:999px;background:#EFF6FF;border:1px solid #3B82F6;color:#1D4ED8;font-size:.85rem;">
        SHOPPER
      </span>

      <button v-if="!me.role" :disabled="busy" @click="login('shopper')">Login shopper</button>
      <button v-if="!me.role" :disabled="busy" @click="login('admin')">Login admin</button>
      <button v-if="me.role" :disabled="busy" @click="logout">Logout</button>
    </div>
  </header>

  <p v-if="err" style="color:#b00">{{ err }}</p>
</template>

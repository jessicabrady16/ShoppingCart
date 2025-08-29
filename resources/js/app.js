import './bootstrap'
import { createApp } from 'vue'
import CartApp from './components/CartApp.vue'

console.log('Mounting Vue…')
createApp(CartApp).mount('#app')

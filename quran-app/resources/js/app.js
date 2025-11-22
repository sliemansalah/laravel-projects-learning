// resources/js/app.js

import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia } from 'pinia'
import App from './App.vue'
import routes from './router'
import './bootstrap'

const app = createApp(App)
const pinia = createPinia()

const router = createRouter({
    history: createWebHistory(),
    routes: routes
})

app.use(pinia)
app.use(router)
app.mount('#app')

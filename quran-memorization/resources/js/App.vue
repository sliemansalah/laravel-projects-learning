<template>
  <div id="app" class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50">
    <router-view />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(() => {
  // Check if user is logged in
  const token = localStorage.getItem('token')
  if (token) {
    authStore.checkAuth()
  }
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Cairo:wght@400;600;700&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Cairo', sans-serif;
  direction: rtl;
}

.arabic-text {
  font-family: 'Amiri', serif;
  font-size: 1.5rem;
  line-height: 2.5rem;
}
</style>

<template>
  <div id="app" class="bg-gradient-to-b from-slate-50 to-slate-100 min-h-screen">
    <!-- Navigation -->
    <nav v-if="authStore.isAuthenticated" class="bg-white shadow sticky top-0 z-40">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo -->
          <div class="text-2xl font-bold text-emerald-600">
            📖 تطبيق القرآن الكريم
          </div>

          <!-- Menu -->
          <div class="flex gap-6 items-center">
            <router-link
              to="/"
              class="text-gray-700 hover:text-emerald-600 transition"
            >
              الرئيسية
            </router-link>
            <router-link
              to="/surahs"
              class="text-gray-700 hover:text-emerald-600 transition"
            >
              السور
            </router-link>
            <router-link
              to="/hifz"
              class="text-gray-700 hover:text-emerald-600 transition"
            >
              الحفظ
            </router-link>
            <router-link
              to="/statistics"
              class="text-gray-700 hover:text-emerald-600 transition"
            >
              الإحصائيات
            </router-link>
            <button
              @click="logout"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition"
            >
              تسجيل الخروج
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto px-4 py-8">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
  authStore.restoreAuth()
})

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

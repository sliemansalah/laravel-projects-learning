<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-500 to-blue-600">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-emerald-600 mb-2">📖</h1>
        <h2 class="text-2xl font-bold text-gray-800">إنشاء حساب جديد</h2>
        <p class="text-gray-600 mt-2">ابدأ رحلتك في حفظ القرآن</p>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <!-- Name -->
        <div>
          <label class="block text-gray-700 font-semibold mb-2">الاسم</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="اسمك الكامل"
          />
        </div>

        <!-- Email -->
        <div>
          <label class="block text-gray-700 font-semibold mb-2">البريد الإلكتروني</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="your@email.com"
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-gray-700 font-semibold mb-2">كلمة المرور</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="••••••••"
          />
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-gray-700 font-semibold mb-2">تأكيد كلمة المرور</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
            placeholder="••••••••"
          />
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ error }}
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="authStore.loading"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg transition disabled:opacity-50"
        >
          {{ authStore.loading ? 'جاري الإنشاء...' : 'إنشاء حساب' }}
        </button>
      </form>

      <!-- Login Link -->
      <div class="text-center mt-6">
        <p class="text-gray-600">هل لديك حساب بالفعل؟</p>
        <router-link to="/login" class="text-emerald-600 hover:text-emerald-700 font-semibold">
          تسجيل الدخول
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from './store/auth'
import { useRouter } from 'vue-router'

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const error = ref(null)
const authStore = useAuthStore()
const router = useRouter()

const handleRegister = async () => {
  try {
    error.value = null
    if (form.value.password !== form.value.password_confirmation) {
      error.value = 'كلمات المرور غير متطابقة'
      return
    }
    await authStore.register(form.value)
    router.push('/')
  } catch (err) {
    error.value = authStore.error
  }
}
</script>

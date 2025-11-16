import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const token = ref(localStorage.getItem('token') || null)
    const loading = ref(false)
    const error = ref(null)

    const isAuthenticated = computed(() => !!token.value)

    async function login(credentials) {
        loading.value = true
        error.value = null

        try {
            const response = await api.post('/auth/login', credentials)

            if (response.data.success) {
                token.value = response.data.data.token
                user.value = response.data.data.user
                localStorage.setItem('token', token.value)
                return true
            }
        } catch (err) {
            error.value = err.response ? .data ? .message || 'حدث خطأ أثناء تسجيل الدخول'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function register(userData) {
        loading.value = true
        error.value = null

        try {
            const response = await api.post('/auth/register', userData)

            if (response.data.success) {
                token.value = response.data.data.token
                user.value = response.data.data.user
                localStorage.setItem('token', token.value)
                return true
            }
        } catch (err) {
            error.value = err.response ? .data ? .message || 'حدث خطأ أثناء التسجيل'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function logout() {
        try {
            await api.post('/auth/logout')
        } catch (err) {
            console.error('Logout error:', err)
        } finally {
            user.value = null
            token.value = null
            localStorage.removeItem('token')
        }
    }

    async function checkAuth() {
        if (!token.value) return false

        try {
            const response = await api.get('/auth/user')
            user.value = response.data.data
            return true
        } catch (err) {
            // Token is invalid
            logout()
            return false
        }
    }

    return {
        user,
        token,
        loading,
        error,
        isAuthenticated,
        login,
        register,
        logout,
        checkAuth
    }
})

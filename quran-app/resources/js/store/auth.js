// resources/js/stores/auth.js

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const token = ref(localStorage.getItem('token') || null)
    const loading = ref(false)
    const error = ref(null)

    const isAuthenticated = computed(() => !!token.value)

    const register = async(data) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.post('/register', data)
            token.value = response.data.data.token
            user.value = response.data.data.user
            localStorage.setItem('token', token.value)
            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
            return response.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'حدث خطأ'
            throw err
        } finally {
            loading.value = false
        }
    }

    const login = async(email, password) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.post('/login', { email, password })
            token.value = response.data.data.token
            user.value = response.data.data.user
            localStorage.setItem('token', token.value)
            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
            return response.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'بيانات الدخول غير صحيحة'
            throw err
        } finally {
            loading.value = false
        }
    }

    const logout = async() => {
        loading.value = true
        try {
            await axios.post('/logout')
            token.value = null
            user.value = null
            localStorage.removeItem('token')
            delete axios.defaults.headers.common['Authorization']
        } catch (err) {
            console.error('Logout error:', err)
        } finally {
            loading.value = false
        }
    }

    const restoreAuth = () => {
        const savedToken = localStorage.getItem('token')
        if (savedToken) {
            token.value = savedToken
            axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
        }
    }

    return {
        user,
        token,
        loading,
        error,
        isAuthenticated,
        register,
        login,
        logout,
        restoreAuth
    }
})
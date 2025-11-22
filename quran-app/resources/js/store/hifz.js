// resources/js/stores/hifz.js

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useHifzStore = defineStore('hifz', () => {
    const userHifz = ref([])
    const userPages = ref([])
    const stats = ref(null)
    const loading = ref(false)
    const error = ref(null)

    const completedSurahs = computed(() =>
        userHifz.value.filter(h => h.status === 'completed').length
    )

    const inProgressSurahs = computed(() =>
        userHifz.value.filter(h => h.status === 'in_progress').length
    )

    const memorizedPages = computed(() =>
        userPages.value.filter(p => p.status === 'memorized').length
    )

    const startHifz = async(surahId, startAyah, endAyah) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.post('/hifz/start', {
                surah_id: surahId,
                start_ayah: startAyah,
                end_ayah: endAyah
            })
            userHifz.value.push(response.data.data)
            return response.data.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'خطأ في بدء الحفظ'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchUserHifz = async() => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get('/hifz/user')
            userHifz.value = response.data.data
            return response.data.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'خطأ في جلب بيانات الحفظ'
            throw err
        } finally {
            loading.value = false
        }
    }

    const updateHifzStatus = async(hifzId, status, memorizedCount = 0, reviewedCount = 0) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.put(`/hifz/${hifzId}/status`, {
                status,
                memorized_count: memorizedCount,
                reviewed_count: reviewedCount
            })
            const index = userHifz.value.findIndex(h => h.id === hifzId)
            if (index !== -1) {
                userHifz.value[index] = response.data.data
            }
            return response.data.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'خطأ في تحديث الحالة'
            throw err
        } finally {
            loading.value = false
        }
    }

    const markPageMemorized = async(pageNumber) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.post('/hifz/page/memorize', {
                page_number: pageNumber
            })
            const existing = userPages.value.find(p => p.page_number === pageNumber)
            if (existing) {
                Object.assign(existing, response.data.data)
            } else {
                userPages.value.push(response.data.data)
            }
            return response.data.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'خطأ في حفظ الصفحة'
            throw err
        } finally {
            loading.value = false
        }
    }

    const reviewPage = async(pageNumber) => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.post('/hifz/page/review', {
                page_number: pageNumber
            })
            const page = userPages.value.find(p => p.page_number === pageNumber)
            if (page) {
                Object.assign(page, response.data.data)
            }
            return response.data.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'خطأ في تسجيل المراجعة'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchUserPages = async() => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get('/hifz/pages')
            userPages.value = response.data.data
            return response.data.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'خطأ في جلب الصفحات'
            throw err
        } finally {
            loading.value = false
        }
    }

    const fetchStats = async() => {
        loading.value = true
        error.value = null
        try {
            const response = await axios.get('/hifz/stats')
            stats.value = response.data.data
            return response.data.data
        } catch (err) {
            error.value = err.response ?.data ?.message || 'خطأ في جلب الإحصائيات'
            throw err
        } finally {
            loading.value = false
        }
    }

    return {
        userHifz,
        userPages,
        stats,
        loading,
        error,
        completedSurahs,
        inProgressSurahs,
        memorizedPages,
        startHifz,
        fetchUserHifz,
        updateHifzStatus,
        markPageMemorized,
        reviewPage,
        fetchUserPages,
        fetchStats
    }
})
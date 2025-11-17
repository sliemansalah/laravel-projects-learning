import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useMemorizationStore = defineStore('memorization', () => {
    const progress = ref(null)
    const todayReview = ref([])
    const currentSession = ref(null)
    const loading = ref(false)
    const error = ref(null)

    const dueItemsCount = computed(() => todayReview.value.length)

    async function startAyah(ayahId) {
        loading.value = true
        error.value = null

        try {
            const response = await api.post('/memorization/start', { ayah_id: ayahId })
            currentSession.value = {
                ayah: response.data.data,
                currentWordIndex: 0,
                correctCount: 0,
                wrongCount: 0,
                startTime: Date.now()
            }
            return response.data.data
        } catch (err) {
            error.value = (err.response && err.response.data && err.response.data.message) || 'حدث خطأ في بدء الحفظ'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function submitWord(wordId, isCorrect) {
        loading.value = true
        error.value = null

        try {
            const response = await api.post('/memorization/submit-word', {
                word_id: wordId,
                is_correct: isCorrect
            })

            if (currentSession.value) {
                if (isCorrect) {
                    currentSession.value.correctCount++
                } else {
                    currentSession.value.wrongCount++
                }
                currentSession.value.currentWordIndex++
            }

            return response.data.data
        } catch (err) {
            error.value = (err.response && err.response.data && err.response.data.message) || 'حدث خطأ في حفظ الكلمة'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function getTodayReview() {
        loading.value = true
        error.value = null

        try {
            const response = await api.get('/memorization/today-review')
            todayReview.value = response.data.data.items
            return response.data.data
        } catch (err) {
            error.value = (err.response && err.response.data && err.response.data.message) || 'حدث خطأ في تحميل المراجعة'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function getProgress() {
        loading.value = true
        error.value = null

        try {
            const response = await api.get('/memorization/progress')
            progress.value = response.data.data
            return progress.value
        } catch (err) {
            error.value = (err.response && err.response.data && err.response.data.message) || 'حدث خطأ في تحميل التقدم'
            throw err
        } finally {
            loading.value = false
        }
    }

    async function completeSession() {
        if (!currentSession.value) return

        const duration = Math.floor((Date.now() - currentSession.value.startTime) / 1000)

        loading.value = true
        error.value = null

        try {
            const response = await api.post('/memorization/complete-session', {
                ayah_id: currentSession.value.ayah.id,
                correct_words: currentSession.value.correctCount,
                wrong_words: currentSession.value.wrongCount,
                duration_seconds: duration
            })

            const sessionData = {...currentSession.value }
            currentSession.value = null

            return {...response.data.data, session: sessionData }
        } catch (err) {
            error.value = (err.response && err.response.data && err.response.data.message) || 'حدث خطأ في إكمال الجلسة'
            throw err
        } finally {
            loading.value = false
        }
    }

    function resetSession() {
        currentSession.value = null
    }

    return {
        progress,
        todayReview,
        currentSession,
        loading,
        error,
        dueItemsCount,
        startAyah,
        submitWord,
        getTodayReview,
        getProgress,
        completeSession,
        resetSession
    }
})
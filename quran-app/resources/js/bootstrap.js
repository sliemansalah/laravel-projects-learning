// resources/js/bootstrap.js

import axios from 'axios'

// تعيين Base URL
axios.defaults.baseURL = '/api'

// إضافة Token من localStorage إلى Headers
const token = localStorage.getItem('token')
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// معالج الأخطاء
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response ?.status === 401) {
            localStorage.removeItem('token')
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

window.axios = axios

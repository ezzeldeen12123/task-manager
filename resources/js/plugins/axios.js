import router from '@/router'
import axios from 'axios'

const axiosIns = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  timeout: 600000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// ℹ️ Add request interceptor to send the authorization header on each subsequent request after login
axiosIns.interceptors.request.use(config => {

  // Retrieve token from localStorage
  const token = localStorage.getItem('accessToken')
  // If token is found
  if (token) {
    // Get request headers and if headers is undefined assign blank object
    config.headers = config.headers || {}

    // Set authorization header
    // ℹ️ JSON.parse will convert token to string
    config.headers.Authorization = token ? `Bearer ${token}` : ''
  }

  // Return modified config
  return config
})

// ℹ️ Add response interceptor to handle 401 response
axiosIns.interceptors.response.use(response => {
  return response
}, error => {
    
  // Handle error
  if (error.response?.status === 401) {

    // ℹ️ Logout user and redirect to login page
    // Remove "user" from localStorage
    localStorage.removeItem('user')

    // Remove "accessToken" from localStorage
    localStorage.removeItem('accessToken')

    // If 401 response returned from api
    router.push('/login')
  }
  else {
    return Promise.reject(error)
  }
})

export default axiosIns

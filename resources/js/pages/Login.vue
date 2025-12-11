<template>
  <div class="fixed inset-0 flex items-center justify-center bg-gray-100">
    <img
      :src="bgImage"
      alt="Background"
      class="absolute inset-0 w-full h-full object-cover"
    />
    <div class="absolute inset-0 bg-black/50"></div>

    <div class="relative z-10 flex flex-col items-center justify-center max-h-screen overflow-hidden">
      <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl">
        <div class="flex flex-col items-center">
          <img
            :src="logoImage"
            alt="Logo"
            class="w-24 mb-4"
          />
          <h1 class="text-center text-xl font-bold mb-6">
            Sistem Informasi Surat Perintah Perjalanan Dinas (SPPD)
          </h1>

          <form @submit.prevent="submitLogin" class="w-full space-y-4">
            <input
              type="text"
              v-model="nip_nipppk"
              placeholder="NIP/NIPPPK"
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            />
            <p v-if="errors.nip_nipppk" class="text-red-600 text-sm mt-1">
              {{ errors.nip_nipppk[0] }}
            </p>

            <input
              type="password"
              v-model="password"
              placeholder="Password"
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            />
            <p v-if="errors.password" class="text-red-600 text-sm mt-1">
              {{ errors.password[0] }}
            </p>

            <button
              type="submit"
              class="w-full bg-green-600 hover:bg-green-400 text-white py-2 rounded"
              :disabled="loading"
            >
              {{ loading ? 'Loading...' : 'Login' }}
            </button>

            <p v-if="errors.general" class="text-red-600 text-sm mt-2 text-center">
              {{ errors.general }}
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

// Import gambar dari assets supaya vite bisa resolve dan optimasi
import bgImage from '../assets/bg.jpeg'
import logoImage from '../assets/Logo_kabupaten_madiun.png'

const router = useRouter()
const nip_nipppk = ref('')
const password = ref('')
const errors = ref({})
const loading = ref(false)

const setAxiosToken = (token) => {
  if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  } else {
    delete axios.defaults.headers.common['Authorization']
  }
}

const submitLogin = async () => {
  loading.value = true
  errors.value = {}

  try {
    const res = await axios.post('/api/login', { nip_nipppk: nip_nipppk.value, password: password.value })

    const userData = { ...res.data.user, role: res.data.role }
    localStorage.setItem('token', res.data.token)
    localStorage.setItem('user', JSON.stringify(userData))

    setAxiosToken(res.data.token)

    router.push(res.data.redirect_to || '/dashboard')
  } catch (err) {
    if (err.response && err.response.status === 422) {
      errors.value = err.response.data.errors || {}
    } else if (err.response && err.response.status === 401) {
      errors.value.general = err.response.data.message
    } else {
      errors.value.general = 'Terjadi kesalahan.'
    }
  } finally {
    loading.value = false
  }
}
</script>
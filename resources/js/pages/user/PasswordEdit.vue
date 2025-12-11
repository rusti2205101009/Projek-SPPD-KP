<template>
  <main class="flex-1 p-6">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
      <h1 class="text-2xl font-bold mb-4">Ubah Password</h1>

      <router-link
        to="/user/dashboard"
        class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
        >
        Kembali
      </router-link>

      <form @submit.prevent="updatePassword" class="space-y-4">
        <div>
          <label class="block text-sm font-semibold mb-1">Password Lama</label>
          <input
            type="password"
            v-model="form.current_password"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            placeholder="Masukkan password lama"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-semibold mb-1">Password Baru</label>
          <input
            type="password"
            v-model="form.new_password"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            placeholder="Masukkan password baru"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-semibold mb-1">Konfirmasi Password Baru</label>
          <input
            type="password"
            v-model="form.new_password_confirmation"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            placeholder="Konfirmasi password baru"
            required
          />
        </div>

        <div class="text-center">
          <button
            type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold"
            :disabled="loading"
          >
            <span v-if="!loading">Simpan</span>
            <span v-else>Memproses...</span>
          </button>
        </div>
      </form>

      <div v-if="message" :class="messageType" class="mt-4 p-2 rounded text-center">
        {{ message }}
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const message = ref('')

const form = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const loading = ref(false)

const updatePassword = async () => {
  loading.value = true

  try {
    const token = localStorage.getItem('token')
    const res = await axios.put('/api/password', form.value, {
      headers: { Authorization: `Bearer ${token}` }
    })

    alert(res.data.message || "Password berhasil diperbarui")
    router.push('/admin/dashboard')

    form.value.current_password = ''
    form.value.new_password = ''
    form.value.new_password_confirmation = ''
  } catch (err) {
    message.value = err.response?.data?.message || 'Terjadi kesalahan'
    alert(err.response?.data?.message || "Gagal memperbarui password")
  } finally {
    loading.value = false
  }
}
</script>
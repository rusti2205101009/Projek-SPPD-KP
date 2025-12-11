<template>
  <main class="flex-1 p-6 overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Edit Profile</h1>
    </div>

    <router-link
      to="/admin/user-profile"
      class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
    >
      Kembali
    </router-link>

    <div class="bg-white rounded shadow max-w-5xl p-6">
      <form @submit.prevent="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold mb-1">Nama Pegawai</label>
            <input type="text" v-model="profile.nama_pegawai" readonly class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">NIP/NIPPPK</label>
            <input type="text" v-model="profile.nip_nipppk" readonly class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed" />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Jenis Kelamin</label>
            <select v-model="profile.jenis_kelamin" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500">
              <option value="">Pilih jenis kelamin</option>
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
          </div>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold mb-1">Email</label>
            <input type="text" v-model="profile.email" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1">No. Handphone</label>
            <input type="text" v-model="profile.nohp" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" />
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Foto</label>
            <input 
              type="file" 
              @change="handleFile" 
              accept="image/*" 
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-500" 
            />

            <div v-if="preview" class="mt-2">
              <img :src="preview" alt="Preview Baru" class="w-24 h-24 rounded object-cover border" />
            </div>

            <div v-else-if="profile.foto" class="mt-2">
              <img :src="profile.foto" alt="Foto Lama" class="w-24 h-24 rounded object-cover border" />
            </div>
          </div>
        </div>

        <div class="md:col-span-2 text-right">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()

const profile = ref({
  nama_pegawai: '',
  nip_nipppk: '',
  jenis_kelamin: '',
  email: '',
  nohp: '',
  foto: null,
})

onMounted(async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get(`/api/user-profiles/${route.params.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    // profile.value = res.data

    const data = res.data.data ?? res.data

    profile.value = {
      ...profile.value,
      nama_pegawai: data.nama_pegawai,
      nip_nipppk: data.nip_nipppk,
      jenis_kelamin: data.jenis_kelamin ?? '',
      email: data.email ?? '',
      nohp: data.nohp ?? '',
      foto: data.foto ? data.foto : null 
    }

  } catch (e) {
    console.error('Gagal load profile:', e)
  }
})

const preview = ref(null)
const newFoto = ref(null)

const handleFile = (e) => {
  const file = e.target.files[0];
  if (file) {
    newFoto.value = file; 
    preview.value = URL.createObjectURL(file);
  } else {
    newFoto.value = null;
    preview.value = null;
  }
};

const updateProfile = async () => {
  try {
    const token = localStorage.getItem('token')
    const formData = new FormData();
    formData.append('jenis_kelamin', profile.value.jenis_kelamin)
    formData.append('email', profile.value.email)
    formData.append('nohp', profile.value.nohp)

    if (newFoto.value) {
      formData.append('foto', newFoto.value)
    }
    
    await axios.post(`/api/user-profiles/${route.params.id}`, formData, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    alert('Profile berhasil diperbarui')
    router.push('/admin/user-profile')
  } catch (e) {
    console.error('Gagal update profile:', e)
    alert('Terjadi kesalahan saat update')
  }
}
</script>
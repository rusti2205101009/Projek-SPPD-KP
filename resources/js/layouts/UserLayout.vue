<template>
  <div class="flex min-h-screen relative">
    <!-- Sidebar for desktop -->
    <UserSidebar class="w-64 hidden md:flex sticky top-0 self-start" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
       <div class="block">
        <UserHeader />
      </div>
      <!-- Mobile Header -->
      <header class="md:hidden flex justify-between items-center p-4 bg-white shadow relative">
        <div class="flex items-center gap-4">
          <button
            id="burger-btn"
            @click="showMobileMenu = !showMobileMenu"
            class="text-gray-700 text-2xl"
          >
            <i class="fas fa-bars"></i>
          </button>
          <h1 class="font-bold text-lg">Dashboard User</h1>
        </div>

        <!-- Profile icon -->
        <button id="profile-btn" @click="showProfileMenu = !showProfileMenu" class="text-gray-600 text-xl">
          <img 
            :src="userFoto || defaultAvatar" 
            class="w-8 h-8 rounded-full object-cover border"
            alt="avatar"
          />
        </button>

        <!-- Popup menu for profile -->
        <div
            v-if="showProfileMenu"
            id="profile-menu"
            class="absolute top-full right-4 mt-2 w-56 bg-white border rounded shadow z-50"
            >
            <!-- Foto, Nama, NIP -->
            <div class="p-4 bg-green-600 text-sm text-gray-800 flex items-center gap-3">
              <img
                :src="userFoto || defaultAvatar"
                alt="Avatar"
                class="w-12 h-12 rounded-full object-cover border shadow-lg"
              />
              <div>
                <p class="font-semibold">{{ userName }}</p>
                <p class="text-xs text-gray-500">{{ userNip }}</p>
              </div>
            </div>
        <hr />

        <div class="flex flex-col items-center p-3 gap-2">
          <p class="text-xs font-semibold">Ganti Foto Profil</p>
          <img 
            v-if="previewFoto" 
            :src="previewFoto" 
            alt="Preview Foto" 
            class="w-16 h-16 rounded-full object-cover border shadow-lg"
          />
          <input 
            type="file" 
            accept="image/*" 
            @change="handleFileChange" 
            class="text-xs w-full"
          />
          <button 
            @click="uploadFoto"
            :disabled="!selectedFile"
            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-bold w-full"
          >
            Upload
          </button>
        </div>
      <hr />

        <!-- tombol vertikal -->
        <div class="flex flex-col text-sm px-4 py-2 gap-2">
            <router-link
              to="/user/user-profile/edit"
              class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-xs font-bold text-center"
              @click="showProfileMenu = false"
            >
              Profile
            </router-link>
            <router-link
              to="/user/password/edit"
              class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-xs font-bold text-center"
              @click="showProfileMenu = false"
            >
              Ubah Sandi
            </router-link>
            <button
              @click="logout"
              class="bg-red-600 text-white hover:bg-red-500 px-3 py-2 rounded text-xs font-bold text-center"
            >
              Logout
            </button>
          </div>
        </div>

        <!-- Mobile Sidebar Menu -->
        <div
          v-if="showMobileMenu"
          id="burger-menu"
          class="absolute top-full left-4 mt-2 w-64 bg-gradient-to-b from-green-600 to-green-900 text-white rounded shadow z-40"
        >
          <nav class="flex flex-col p-4 space-y-2">
            <router-link
              v-for="item in navItems"
              :key="item.path"
              :to="item.path"
              @click="showMobileMenu = false"
              class="block px-3 py-2 rounded hover:bg-green-600"
            >
              <i :class="item.icon"></i> {{ item.label }}
            </router-link>
          </nav>
        </div>
      </header>

      <!-- Main view -->
      <main class="flex-1 p-6 overflow-auto min-w-0 min-h-0">
        <div class="overflow-x-auto h-full">
        <router-view />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import UserSidebar from '../components/UserSidebar.vue'
import UserHeader from '../components/UserHeader.vue'
import defaultAvatar from '../assets/user-avatar.png'

const showMobileMenu = ref(false)
const showProfileMenu = ref(false)

const userName = ref('')
const userNip = ref('')
const userFoto = ref('')

const previewFoto = ref(null)
const selectedFile = ref(null)
const userId = ref(null)

const router = useRouter()

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (!file) return
  selectedFile.value = file
  previewFoto.value = URL.createObjectURL(file)
}

const uploadFoto = async () => {
  if (!selectedFile.value) return
  const formData = new FormData()
  formData.append('foto', selectedFile.value)

  try {
    const token = localStorage.getItem('token')
    const res = await axios.post(`/api/users/${userId.value}/update-photo`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Authorization: `Bearer ${token}`,
      }
    })

    userFoto.value = res.data.foto
    previewFoto.value = null
    selectedFile.value = null
    showProfileMenu.value = false
  } catch (err) {
    console.error('Upload gagal:', err)
    alert('Gagal mengupload foto')
  }
}

const logout = async () => {
  try {
    await axios.post('/api/logout') 
  } catch (e) {
    console.error('Logout error:', e)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

const handleClickOutside = (e) => {
  const profileMenu = document.getElementById('profile-menu')
  const burgerMenu = document.getElementById('burger-menu')
  const profileBtn = document.getElementById('profile-btn')
  const burgerBtn = document.getElementById('burger-btn')

  if (
    showProfileMenu.value &&
    profileMenu &&
    !profileMenu.contains(e.target) &&
    !profileBtn.contains(e.target)
  ) {
    showProfileMenu.value = false
  }

  if (
    showMobileMenu.value &&
    burgerMenu &&
    !burgerMenu.contains(e.target) &&
    !burgerBtn.contains(e.target)
  ) {
    showMobileMenu.value = false
  }
}

onMounted(async () => {
  document.addEventListener('click', handleClickOutside)

  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }
    const res = await axios.get('/api/me', {
      headers: { Authorization: `Bearer ${token}` }
    })
    userName.value = res.data.nama_pegawai
    userNip.value = res.data.nip_nipppk
    userFoto.value = res.data.foto
  } catch (e) {
    console.error('Gagal fetch user:', e)
    router.push('/login')
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

const navItems = [
  { path: '/user/dashboard', label: 'Dashboard', icon: 'fa-solid fa-house' },
  { path: '/user/pegawai', label: 'Data Pegawai', icon: 'fa-solid fa-users' },
  { path: '/user/spt', label: 'Data STP', icon: 'fa-solid fa-file-lines' },
  { path: '/user/sppd', label: 'Data SPPD', icon: 'fa-solid fa-file-contract' },
  { path: '/user/rincian-biaya', label: 'Rincian Biaya', icon: 'fa-solid fa-coins' },
  { path: '/user/keberangkatan', label: 'Keberangkatan', icon: 'fa-solid fa-plane-departure' },
  { path: '/user/kepulangan', label: 'Kepulangan', icon: 'fa-solid fa-plane-arrival' },
  // { path: '/user/laporan', label: 'Cetak Laporan', icon: 'fa-solid fa-print' },
]
</script>
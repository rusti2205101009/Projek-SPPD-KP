<template>
  <div class="flex min-h-screen relative">
    <!-- Sidebar for desktop -->
    <AdminSidebar class="w-64 hidden md:flex sticky top-0 self-start" />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
       <div class="block">
        <AdminHeader />
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
          <h1 class="font-bold text-lg">Dashboard Admin</h1>
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
            <div class="p-4 bg-blue-600 text-sm text-white flex items-center gap-3">
              <img
                :src="userFoto || defaultAvatar"
                alt="Avatar"
                class="w-12 h-12 rounded-full object-cover border shadow-lg"
              />
              <div>
                <p class="font-semibold">{{ userName }}</p>
                <p class="text-xs">{{ userNip }}</p>
              </div>
            </div>
        <hr />

        <!-- tombol vertikal -->
        <div class="flex flex-col text-sm px-4 py-2 gap-2">
            <router-link
              to="/user/user-profile/edit"
              class="bg-blue-600 hover:bg-blue-800 text-white px-3 py-2 rounded text-xs font-bold text-center"
              @click="showProfileMenu = false"
            >
              Profile
            </router-link>
            <router-link
              to="/user/password/edit"
              class="bg-blue-600 hover:bg-blue-800 text-white px-3 py-2 rounded text-xs font-bold text-center"
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
          class="absolute top-full left-4 mt-2 w-64 bg-gradient-to-b from-blue-600 to-blue-900 text-white rounded shadow z-40"
        >

        <nav class="flex flex-col p-4 space-y-2">
            <div v-for="item in navItems" :key="item.label">
              <!-- Cetak Laporan -->
              <div v-if="item.label === 'Cetak Laporan'" class="relative">
                <div
                  class="cetak-laporan-btn flex items-center gap-3 px-4 py-2 hover:bg-blue-600 w-full rounded-lg font-bold cursor-pointer"
                  @click="toggleDropdown"
                >
                  <i :class="item.icon"></i>
                  {{ item.label }}
                  <i v-if="isLoading" class="fa-solid fa-spinner fa-spin ml-2 text-sm"></i>
                </div>
                <div
                  v-if="showDropdown"
                  class="cetak-laporan-dropdown absolute left-0 mt-2 w-full bg-white text-black rounded-lg shadow-lg max-h-40 overflow-y-auto z-50"
                >
                  <ul>
                    <li
                      v-for="year in years"
                      :key="year"
                      @click="confirmAndDownload(year)"
                      class="px-4 py-2 hover:bg-blue-100 cursor-pointer"
                    >
                      {{ year }}
                    </li>
                  </ul>
                </div>
              </div>

              <router-link
                v-else
                :to="item.path"
                @click="showMobileMenu = false"
                class="block px-3 py-2 rounded hover:bg-blue-600"
              >
                <i :class="item.icon"></i> {{ item.label }}
              </router-link>
            </div>
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
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AdminSidebar from '../components/AdminSidebar.vue'
import AdminHeader from '../components/AdminHeader.vue'
import defaultAvatar from '../assets/user-avatar.png'

const router = useRouter()
const route = useRoute()

const showMobileMenu = ref(false)
const showProfileMenu = ref(false)

const userName = ref('')
const userNip = ref('')
const userFoto = ref('')

const isLoading = ref(false)
const years = ref([])
const showDropdown = ref(false)

function toggleDropdown() {
  showDropdown.value = !showDropdown.value
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
  const dropdown = document.querySelector('.cetak-laporan-dropdown')
  const button = document.querySelector('.cetak-laporan-btn')
  if (
    dropdown &&
    !dropdown.contains(event.target) &&
    button &&
    !button.contains(event.target)
  ) {
    showDropdown.value = false
  }
  
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

const confirmAndDownload = async (year) => {
  if (!year) return
  const confirmDownload = confirm(`Apakah anda mau mencetak rekap tahun ${year}?`)
  if (!confirmDownload) return
  try {
    isLoading.value = true
    const token = localStorage.getItem('token')
    const response = await axios.get(`/api/export/full-rekap?year=${year}`, {
      responseType: 'blob',
      headers: { Authorization: `Bearer ${token}` },
    })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `rekap-${year}.xlsx`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  } catch (err) {
    console.error('DETAIL ERROR:', err.message, err)
  } finally {
    isLoading.value = false
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

    const yearRes = await axios.get('/api/years', {
      headers: { Authorization: `Bearer ${token}` },
    })
    years.value = yearRes.data
  } catch (e) {
    console.error('Gagal fetch user:', e)
    router.push('/login')
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

const navItems = [
  { path: '/admin/dashboard', label: 'Dashboard', icon: 'fa-solid fa-house' },
  { path: '/admin/user', label: 'Data User', icon: 'fa-solid fa-user' },
  // { path: '/admin/user-profile', label: 'Data Profile User', icon: 'fa-solid fa-id-badge' },
  { path: '/admin/pegawai', label: 'Data Pegawai', icon: 'fa-solid fa-users' },
  { path: '/admin/kepala', label: 'Data Kepala', icon: 'fa-solid fa-user-tie' },
  { path: '/admin/spt', label: 'Data STP', icon: 'fa-solid fa-file-lines' },
  { path: '/admin/sppd', label: 'Data SPPD', icon: 'fa-solid fa-file-contract' },
  { path: '/admin/rincian-biaya', label: 'Rincian Biaya', icon: 'fa-solid fa-coins' },
  { path: '/admin/keberangkatan', label: 'Keberangkatan', icon: 'fa-solid fa-plane-departure' },
  { path: '/admin/kepulangan', label: 'Kepulangan', icon: 'fa-solid fa-plane-arrival' },
  { path: '/admin/template', label: 'Template', icon: 'fa-solid fa-envelope' },
  { label: 'Cetak Laporan', icon: 'fa-solid fa-print' },
]
</script>
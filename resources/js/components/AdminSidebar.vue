<template>
  <aside class="h-screen w-64  bg-gradient-to-b from-blue-600 to-blue-900 text-white flex flex-col shadow-xl overflow-y-auto">
    <!-- Header user -->
    <div class="p-6 flex flex-col items-center border-b border-blue-600">
      <img 
          :src="userFoto || defaultAvatar" 
          alt="Avatar" 
          class="w-24 h-24 rounded-full object-cover border-2 border-white shadow-lg"
        />
      <div class="mt-4 text-center">
        <p class="text-lg font-semibold truncate w-40">{{ userName }}</p>
        <p class="text-sm text-blue-200 truncate w-40">{{ userNip }}</p>
      </div>
    </div>

    <!-- Spinner loading -->
    <div v-if="isLoading" class="flex items-center justify-center mt-4 p-4 bg-blue-500 rounded-full text-white text-sm animate-pulse">
      <i class="fa-solid fa-spinner fa-spin mr-2"></i> Sedang memproses laporan...
    </div>

    <!-- Navigation menu -->
    <div class="flex-1 flex flex-col justify-between">
      <nav class="px-4 py-4 space-y-2 overflow-y-auto">
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

            <!-- Dropdown langsung nempel -->
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
          class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-600"
          :class="{ 'bg-blue-600 font-bold': isActive(item.path) }"
        >
          <i :class="item.icon"></i> {{ item.label }}
        </router-link>
        </div>
      </nav>

      <!-- Logout -->
      <div class="p-4 border-t border-blue-600">
        <button
          @click="logout"
          class="w-full flex items-center justify-center gap-2 px-4 py-2 text-xs bg-red-600 hover:bg-red-500 rounded font-semibold shadow-md transition-all duration-200"
        >
          <i class="fa-solid fa-sign-out-alt"></i> Logout
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import defaultAvatar from '../assets/user-avatar.png'

const router = useRouter()
const route = useRoute()

const userName = ref('')
const userNip = ref('')
const userFoto = ref('')

const isLoading = ref(false);

const years = ref([]);
const showDropdown = ref(false);

function toggleDropdown() {
  showDropdown.value = !showDropdown.value
}

function handleClickOutside(event) {
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

     // Fetch tahun distinct dari API
    const yearRes = await axios.get("/api/years", {
      headers: { Authorization: `Bearer ${token}` },
    });
    years.value = yearRes.data; 
  } catch (e) {
    console.error('Gagal fetch user:', e)
    router.push('/login')
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

const logout = async () => {
  try {
    await axios.post('/api/logout') // hapus token di server
  } catch (e) {
    console.error('Logout error:', e)
    // biarin tetap lanjut hapus localStorage meskipun request gagal
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

const isActive = (path) => route.path.startsWith(path)

// Fungsi download laporan
// const downloadLaporan = async () => {
//   try {
//     isLoading.value = true
//     const token = localStorage.getItem('token')
//     const response = await axios.get('/export/full-rekap', {
//       responseType: 'blob',
//       headers: { Authorization: `Bearer ${token}` }
//     })

//     const url = window.URL.createObjectURL(new Blob([response.data]))
//     const link = document.createElement('a')
//     link.href = url
//     link.setAttribute('download', 'full-rekap.xlsx')
//     document.body.appendChild(link)
//     link.click()
//     document.body.removeChild(link)
//   } catch (err) {
//     console.error("DETAIL ERROR:",  err.message, err);
//   } finally {
//     isLoading.value = false
//   }
// }

// Konfirmasi dan download laporan
const confirmAndDownload = async (year) => {
  if (!year) return;

  const confirmDownload = confirm(
    `Apakah anda mau mencetak rekap tahun ${year}?`
  );

  if (!confirmDownload) return;

  try {
    isLoading.value = true;
    const token = localStorage.getItem("token");
    const response = await axios.get(
      `/api/export/full-rekap?year=${year}`,
      {
        responseType: "blob",
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `rekap-${year}.xlsx`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (err) {
    console.error("DETAIL ERROR:", err.message, err);
  } finally {
    isLoading.value = false;
    // year.value = "";
  }
};

const navItems = [
  { path: '/admin/dashboard', label: 'Dashboard', icon: 'fa-solid fa-house' },
  { path: '/admin/user', label: 'Data User', icon: 'fa-solid fa-user' },
  // { path: '/admin/user-profile', label: 'Data Profile User', icon: 'fa-solid fa-id-badge' },
  { path: '/admin/pegawai', label: 'Data Pegawai', icon: 'fa-solid fa-users' },
  { path: '/admin/kepala', label: 'Data Kepala Bagian', icon: 'fa-solid fa-user-tie' },
  { path: '/admin/spt', label: 'Data SPT', icon: 'fa-solid fa-file-lines' },
  { path: '/admin/sppd', label: 'Data SPPD', icon: 'fa-solid fa-file-contract' },
  { path: '/admin/rincian-biaya', label: 'Rincian Biaya', icon: 'fa-solid fa-coins' },
  { path: '/admin/keberangkatan', label: 'Keberangkatan', icon: 'fa-solid fa-plane-departure' },
  { path: '/admin/kepulangan', label: 'Kepulangan', icon: 'fa-solid fa-plane-arrival' },
  { path: '/admin/template', label: 'Template', icon: 'fa-solid fa-envelope' },
  { label: 'Cetak Laporan', icon: 'fa-solid fa-print' },
]
</script>
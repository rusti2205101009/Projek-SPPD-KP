<template>
  <aside class="h-screen w-64  bg-gradient-to-b from-green-600 to-green-900 text-white flex flex-col shadow-xl overflow-y-auto">
    <div class="p-6 flex flex-col items-center border-b border-green-600">
      <div class="relative w-24 h-24">
       <img 
          :src="userFoto || defaultAvatar" 
          alt="Avatar" 
          class="w-24 h-24 rounded-full object-cover border-2 border-white shadow-lg"
        />

      <button
        @click="triggerFileInput"
        class="absolute bottom-0 right-0 bg-white text-blue-700 w-7 h-7 rounded-full flex items-center justify-center shadow-md hover:bg-gray-200"
        title="Ubah Foto"
      >
        <i class="fa fa-pencil text-xs"></i>
      </button>
     </div>
      <input
        ref="fileInput"
        type="file"
        accept="image/*"
        class="hidden"
        @change="onFileChange"
      />

      <div class="mt-4 text-center">
        <p class="text-lg font-semibold truncate w-40">{{ userName }}</p>
        <p class="text-sm text-green-200 truncate w-40">{{ userNip }}</p>
      </div>

      <div v-if="isLoading" class="flex items-center justify-center mt-4 p-4 bg-green-600 rounded-full text-white text-sm animate-pulse">
        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Sedang memproses laporan...
      </div>

      <div class="flex justify-center gap-2 mt-4 w-full">
        <router-link
            to="/user/password/edit"
            class="bg-green-500 hover:bg-white/30 text-white px-3 py-1 rounded text-xs font-bold"
          >
            Ubah Sandi
        </router-link>
      </div>
    </div>

    <div class="flex-1 flex flex-col justify-between">
      <nav class="px-4 py-4 space-y-2 overflow-y-auto">
        <div v-for="item in navItems" :key="item.label">

          <div v-if="item.label === 'Cetak Laporan'" class="relative">
            <div
              class="cetak-laporan-btn flex items-center gap-3 px-4 py-2 hover:bg-green-600 w-full rounded-lg font-bold cursor-pointer"
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
            class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-600"
            :class="{ 'bg-green-600 font-bold': isActive(item.path) }"
          >
            <i :class="item.icon"></i> {{ item.label }}
          </router-link>
        </div>
      </nav>

      <div class="p-4 border-t border-green-600">
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
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import defaultAvatar from '../assets/user-avatar.png'

const router = useRouter()
const route = useRoute()
const isLoading = ref(false)

const userName = ref('')
const userNip = ref('')
const userFoto = ref('')

const previewFoto = ref(null)
const fileInput = ref(null)

const triggerFileInput = () => {
  fileInput.value?.click()
}

const onFileChange = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  previewFoto.value = URL.createObjectURL(file)

  const formData = new FormData()
  formData.append('foto', file)

  try {
    const token = localStorage.getItem('token')
    const res = await axios.post('/api/user/photo', formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    })
    userFoto.value = res.data.foto
    previewFoto.value = null 
    alert('Foto berhasil diperbarui')
  } catch (e) {
    console.error('Gagal upload foto:', e)
    alert('Gagal upload foto')
  }
}

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
    await axios.post('/api/logout') 
  } catch (e) {
    console.error('Logout error:', e)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

const isActive = (path) => route.path.startsWith(path)

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
  }
};

const navItems = [
  { path: '/user/dashboard', label: 'Dashboard', icon: 'fa-solid fa-house' },
  { path: '/user/pegawai', label: 'Data Pegawai', icon: 'fa-solid fa-users' },
  { path: '/user/spt', label: 'Data SPT', icon: 'fa-solid fa-file-lines' },
  { path: '/user/sppd', label: 'Data SPPD', icon: 'fa-solid fa-file-contract' },
  { path: '/user/rincian-biaya', label: 'Rincian Biaya', icon: 'fa-solid fa-coins' },
  { path: '/user/keberangkatan', label: 'Keberangkatan', icon: 'fa-solid fa-plane-departure' },
  { path: '/user/kepulangan', label: 'Kepulangan', icon: 'fa-solid fa-plane-arrival' },
  // { label: 'Cetak Laporan', icon: 'fa-solid fa-print' },
]
</script>
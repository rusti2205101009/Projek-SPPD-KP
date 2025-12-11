<template>
  <div class="p-6 bg-white rounded shadow max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Data Profile User</h1>

    <div class="flex items-center gap-4 mb-6">
      <input
        v-model="search"
        type="text"
        placeholder="--Cari nama pegawai--"
        class="border rounded px-3 py-2 flex-1"
      />
    </div>

    <div class="overflow-x-auto">
      <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
        <thead class="bg-gray-200 text-center">
          <tr>
            <th class="px-4 py-2 border">No</th>
            <th class="px-4 py-2 border">Foto</th>
            <th class="px-4 py-2 border">NIP/NIPPPK</th>
            <th class="px-4 py-2 border">Nama</th>
            <th class="px-4 py-2 border">Jenis Kelamin</th>
            <th class="px-4 py-2 border">Email</th>
            <th class="px-4 py-2 border">No. HP</th>
            <th class="px-4 py-2 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(profile, i) in profiles"
            :key="profile.id"
            class="hover:bg-gray-50"
          >
            <td class="border px-4 py-2 text-center">{{ getRowNumber(i) }}</td>
            <td class="border px-4 py-2 text-center">
            <img
                v-if="profile.foto"
                :src="profile.foto"
                alt="Foto Pegawai"
                class="w-12 h-12 object-cover rounded"
                @error="(e) => (e.target.src = 'https://via.placeholder.com/48?text=No+Image')"
            />
            <span v-else class="text-gray-400">-</span>
            </td>
            <td class="border px-4 py-2">{{ profile.nip_nipppk || '-' }}</td>
            <td class="border px-4 py-2">{{ profile.nama || '-' }}</td>
            <td class="border px-4 py-2 text-center">
              {{ profile.jenis_kelamin || '-' }}
            </td>
            <td class="border px-4 py-2">{{ profile.email || '-' }}</td>
            <td class="border px-4 py-2">{{ profile.nohp || '-' }}</td>
            <td class="px-4 py-2 border">
              <div class="flex gap-1">
                <div class="relative group">
                  <router-link
                    :to="`/admin/user-profile/edit/${profile.id}`"
                    class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-600 text-white hover:bg-blue-700"
                  >
                    <i class="fa fa-pen text-xs"></i>
                  </router-link>
                  <span
                    class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition pointer-events-none"
                  >
                    Edit
                  </span>
                </div>
              </div>
            </td>
          </tr>

          <tr v-if="profiles.length === 0">
            <td colspan="8" class="border px-4 py-4 text-center text-gray-500">
              Tidak ada data
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-end gap-2 items-center">
      <button
        @click="currentPage--"
        :disabled="currentPage === 1"
        class="px-3 py-1 border rounded disabled:opacity-50"
      >
        Prev
      </button>
      <span>Halaman {{ currentPage }} dari {{ totalPages }}</span>
      <button
        @click="currentPage++"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 border rounded disabled:opacity-50"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const profiles = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const search = ref('')

const perPage = 10

const fetchProfile = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get('/api/user-profiles', {
      headers: { Authorization: `Bearer ${token}` },
      params: {
        page: currentPage.value,
        per_page: perPage,
        search: search.value
      }
    })
    profiles.value = Array.isArray(res.data?.data) ? res.data.data : []
    currentPage.value = Number(res.data?.meta?.current_page) || 1
    totalPages.value = Number(res.data?.meta?.last_page) || 1
  } catch (e) {
    console.error('Gagal load data:', e)
    alert('Gagal memuat data')
    router.push('/dashboard')
  }
}

const getRowNumber = (index) => ((currentPage.value - 1) * perPage) + index + 1

watch(search, () => {
  currentPage.value = 1
  fetchProfile()
})

watch(currentPage, () => {
  fetchProfile()
})

onMounted(fetchProfile)
</script>
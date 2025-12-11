<template>
  <h1 class="text-2xl font-bold mb-6">Data User</h1>

  <div class="flex items-center gap-4 mb-6">
    <input
      v-model="search"
      type="text"
      placeholder="--cari disini--"
      class="border rounded px-3 py-2 flex-1"
    />

    <router-link
      to="/admin/user/create"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
    >
      Tambah User
    </router-link>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full border border-gray-300 rounded-lg overflow-hidden text-sm">
      <thead class="bg-gray-200 text-center">
        <tr>
          <th class="px-4 py-2 border">No</th>
          <th class="px-4 py-2 border">Foto</th>
          <th class="px-4 py-2 border">Nama Pegawai</th>
          <th class="px-4 py-2 border">NIP/NIPPPK</th>
          <th class="px-4 py-2 border">Role</th>
          <th class="px-4 py-2 border">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(user, i) in users"
          :key="user.id"
          :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
        >
          <td class="px-4 py-2 border">{{ getRowNumber(i) }}</td>
          <td class="px-4 py-2 border text-center">
            <img
              v-if="user.foto"
              :src="user.foto"
              alt="Foto User"
              class="w-10 h-10 rounded-full object-cover mx-auto"
            />
            <span v-else class="text-gray-400 text-xs">-</span>
          </td>
          <td class="px-4 py-2 border">
            {{ user.pegawai ? user.pegawai.nama_pegawai : '-' }}
          </td>
          <td class="px-4 py-2 border">{{ user.nip_nipppk }}</td>
          <td class="px-4 py-2 border">{{ user.role }}</td>
          <td class="px-4 py-2 border">
            <div class="flex gap-1">
              <div class="relative group">
                <router-link
                  :to="`/admin/user/edit/${user.id}`"
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
              <div class="relative group">
                <button
                  @click="deleteItem(user.id)"
                  class="w-7 h-7 flex items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600"
                >
                  <i class="fa fa-trash text-xs"></i>
                </button>
                <span
                  class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition pointer-events-none"
                >
                  Hapus
                </span>
              </div>
            </div>
          </td>
        </tr>
        <tr v-if="!users || users.length === 0">
          <td class="px-4 py-4 border text-center" colspan="5">
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
</template>

<script setup>
import { ref, onMounted, watch } from "vue"
import axios from "axios"

const users = ref([])
const currentPage = ref(1)
const search = ref("")
const totalPages = ref(1)

const perPage = 10

const fetchUser = async () => {
  try {
    const res = await axios.get("/api/users", {
      params: {
        page: currentPage.value,
        per_page: perPage,
        search: search.value
      }
    })
    users.value = Array.isArray(res.data?.data) ? res.data.data : []
    currentPage.value = Number(res.data?.meta?.current_page) || 1
    totalPages.value = Number(res.data?.meta?.last_page) || 1
  } catch (e) {
    console.error("DETAIL ERROR:", e.response?.data);
    data.value = []
  }
}

const getRowNumber = (index) =>
  ((currentPage.value - 1) * perPage) + index + 1

watch(search, () => {
  currentPage.value = 1
  fetchUser()
})

watch(currentPage, () => {
  fetchUser()
})

onMounted(fetchUser)

const deleteItem = async (id) => {
  if (confirm("Yakin ingin hapus user ini?")) {
    try {
      await axios.delete(`/api/users/${id}`)
      alert("User berhasil dihapus")
      fetchUser()
    } catch (e) {
      alert("Gagal menghapus user: " + e.message)
    }
  }
}
</script>
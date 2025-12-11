<template>
        <h1 class="text-2xl font-bold">Data Kepala Bagian</h1>

      <div class="flex items-center gap-4 mb-6">
        <input
          v-model="search"
          type="text"
          placeholder="--cari disini--"
          class="border rounded px-3 py-2 flex-1"
        />

        <router-link
          to="/admin/kepala/create"
          class="ml-auto bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Tambah Data
        </router-link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
          <thead class="bg-gray-200 text-center">
            <tr>
              <th class="px-4 py-2 border">No</th>
              <th class="px-4 py-2 border">Foto</th>
              <th class="px-4 py-2 border">NIP</th>
              <th class="px-4 py-2 border">Nama</th>
              <th class="px-4 py-2 border">Pangkat</th>
              <th class="px-4 py-2 border">Golongan</th>
              <th class="px-4 py-2 border">Jabatan</th>
              <th class="px-4 py-2 border">Tanda Tangan</th>
              <th class="px-4 py-2 border">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(head, i) in heads"
              :key="head.id"
              class="hover:bg-gray-50"
            >
              <td class="border px-4 py-2 text-center">{{ getRowNumber(i) }}</td>
              <td class="border px-4 py-2 text-center">
                <img
                  :src="head.foto || defaultAvatar"
                  alt="Foto Pegawai"
                  class="w-12 h-12 object-cover rounded"
                />
              </td>
              <td class="border px-4 py-2">{{ head.nip }}</td>
              <td class="border px-4 py-2">{{ formatNama(head) }}</td>
              <td class="border px-4 py-2">{{ head.pangkat }}</td>
              <td class="border px-4 py-2">{{ head.golongan }}</td>
              <td class="border px-4 py-2">{{ head.jabatan }}</td>
              <td class="border px-4 py-2 text-center">
                <img
                  v-if="head.ttd"
                  :src="head.ttd"
                  alt="Tanda Tangan"
                  class="w-20 h-12 object-contain"
                />
              </td>
              <td class="px-4 py-2 border">
              <div class="flex gap-1">
                <div class="relative group">
                  <router-link
                    :to="`/admin/kepala/edit/${head.id}`"
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
                    @click="deleteItem(head.id)"
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
            <tr v-if="heads.length === 0">
              <td colspan="9" class="border px-4 py-2 text-center text-gray-500">
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
  import { ref, watch, onMounted } from "vue";
  import axios from "axios";
  import defaultAvatar from '../../../assets/user-avatar.png'

  const heads = ref([]);
  const currentPage = ref(1);
  const totalPages = ref(1);
  const search = ref("");

  const perPage = 10

  const fetchHeadDivi = async () => {
    try {
      const res = await axios.get("/api/head_divisions", {
        params: {
          page: currentPage.value,
          per_page: perPage,
          search: search.value,
        },
      });
      heads.value = Array.isArray(res.data?.data) ? res.data.data : []
      currentPage.value = Number(res.data?.meta?.current_page) || 1
      totalPages.value = Number(res.data?.meta?.last_page) || 1
    } catch (error) {
      alert("Gagal memuat data: " + error.message);
    }
  };

  const getRowNumber = (index) => ((currentPage.value - 1) * perPage) + index + 1

  const deleteItem = async (id) => {
    if (confirm("Apakah anda yakin ingin menghapus data ini?")) {
      try {
        await axios.delete(`/api/head_divisions/${id}`);
        alert("Data berhasil dihapus");
        fetchHeadDivi();
      } catch (error) {
        alert("Gagal menghapus data: " + error.message);
      }
    }
  };

  const formatNama = (kepala) => {
    const depan = kepala.gelar_depan ? kepala.gelar_depan.trim() + " " : "";
    const belakang = kepala.gelar_belakang
      ? ", " + kepala.gelar_belakang.trim()
      : "";
    const nama = kepala.nama_kepala || "";
    return `${depan}${nama}${belakang}`;
  };

  watch(search, () => {
    currentPage.value = 1
    fetchHeadDivi()
  })

  watch(currentPage, () => {
    fetchHeadDivi()
  })

  onMounted(fetchHeadDivi)
</script> 
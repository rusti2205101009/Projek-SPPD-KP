<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Data Kepulangan</h1>
    </div>

    <div class="flex items-center gap-4 mb-6">
      <input
        v-model="search"
        type="text"
        placeholder="--cari disini--"
        class="border rounded px-3 py-2 flex-1"
      />
    </div>

    <div class="overflow-x-auto">
      <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
        <thead class="bg-gray-200 text-left">
          <tr>
            <th class="px-4 py-2 border">No</th>
            <th class="px-4 py-2 border">Nama Pegawai</th>
            <th class="px-4 py-2 border">Nomor Surat</th>
            <th class="px-4 py-2 border">Nomor Bukti</th>
            <th class="px-4 py-2 border">Tanggal Bukti</th>
            <th class="px-4 py-2 border">Nomor Tiket Pulang</th>
            <th class="px-4 py-2 border">Nama Maskapai/Kereta/Travel/Bis</th>
            <th class="px-4 py-2 border">Tempat Asal</th>
            <th class="px-4 py-2 border">Daerah Asal</th>
            <th class="px-4 py-2 border">Tempat Tujuan</th>
            <th class="px-4 py-2 border">Tanggal Pulang Tiket</th>
            <th class="px-4 py-2 border">Bukti Lampiran</th>
            <th class="px-4 py-2 border">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(pulang, i) in data"
            :key="pulang.id"
            class="odd:bg-white even:bg-gray-50"
          >
            <td class="px-4 py-2 border">{{ getRowNumber(i) }}</td>
            <td class="px-4 py-2 border">
              {{ pulang.pegawai?.nama_pegawai }}
            </td>
            <td class="px-4 py-2 border">{{ pulang.nomor_surat }}</td>
            <td class="px-4 py-2 border">{{ pulang.no_bukti }}</td>
            <td class="px-4 py-2 border">{{ pulang.tanggal_bukti }}</td>
            <td class="px-4 py-2 border">{{ pulang.no_tiket_kembali }}</td>
            <td class="px-4 py-2 border">{{ pulang.nama_transportasi }}</td>
            <td class="px-4 py-2 border">{{ pulang.tempat_asal }}</td>
            <td class="px-4 py-2 border">{{ pulang.daerah_asal }}</td>
            <td class="px-4 py-2 border">{{ pulang.tempat_tujuan }}</td>
            <td class="px-4 py-2 border">{{ pulang.tanggal_kembali_tiket }}</td>
            <td class="px-4 py-2 border text-center">
              <template v-if="pulang.bukti_file_pulang_url">
                <span class="block text-xs text-green-600 mb-1">Sudah upload</span>
                <div class="flex gap-1 justify-center">
                  <!-- Lihat -->
                  <a
                    :href="pulang.bukti_file_pulang_url"
                    target="_blank"
                    class="w-7 h-7 flex items-center justify-center rounded-full bg-yellow-500 text-white hover:bg-yellow-600"
                    title="Lihat"
                  >
                    <i class="fa fa-eye text-xs"></i>
                  </a>
                </div>
              </template>
              <template v-else>
                <span class="text-xs text-red-500">Belum upload</span>
              </template>
            </td>
            <td class="px-4 py-2 border">
              <div class="flex gap-1">
                <div class="relative group">
                  <router-link
                    :to="`/admin/kepulangan/edit/${pulang.id}`"
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
          <tr v-if="data.length === 0">
            <td colspan="11" class="text-center py-4 text-gray-500">
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
import { ref, onMounted, watch } from "vue"
import axios from "axios"

const data = ref([])
const search = ref("")
const currentPage = ref(1)
const totalPages = ref(1)

const perPage = 10

const fetchReturns = async () => {
  try {
    const res = await axios.get("/api/returns", {
      params: {
        search: search.value,
        per_page: perPage,
        page: currentPage.value,
      },
    })
    data.value = Array.isArray(res.data?.data) ? res.data.data : [];
    currentPage.value = Number(res.data?.meta?.current_page) || 1;
    totalPages.value = Number(res.data?.meta?.last_page) || 1;
  } catch (e) {
    console.error("Gagal ambil data sppd", e.response?.data || e.message);
    data.value = [];
  }
}

const getRowNumber = (index) => ((currentPage.value - 1) * perPage) + index + 1

const formatNama = (pegawai) => {
  return `${pegawai.gelar_depan ?? ''} ${pegawai.nama_pegawai ?? ''} ${pegawai.gelar_belakang ?? ''}`.replace(/\s+/g, ' ').trim()
}

watch(search, () => {
  currentPage.value = 1
  fetchReturns()
})

watch(currentPage, () => {
  fetchReturns()
})

onMounted(fetchReturns)
</script>   
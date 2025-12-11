<template>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Rincian Biaya</h1>
    </div>

     <div class="flex items-center gap-4 mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="--cari disini--"
        class="border rounded px-3 py-2 flex-1"
      />
    </div>

    <div class="overflow-x-auto">
      <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
        <thead class="bg-gray-200 text-center">
          <tr>
            <th class="px-4 py-2 border">No</th>
            <th class="px-4 py-2 border">Nama Pegawai</th>
            <th class="px-4 py-2 border">Nomor Surat</th>
            <th class="px-4 py-2 border">Uang Perhari</th>
            <th class="px-4 py-2 border">Total Perhari</th>
            <th class="px-4 py-2 border">Uang Representasi</th>
            <th class="px-4 py-2 border">Nama Penginapan/Hotel</th>
            <th class="px-4 py-2 border">Biaya Akomodasi</th>
            <th class="px-4 py-2 border">Biaya Lain/Kontribusi</th>
            <th class="px-4 py-2 border">Biaya Tiket Berangkat</th>
            <th class="px-4 py-2 border">Biaya Tiket Kembali</th>
            <th class="px-4 py-2 border">Biaya Transport/BBM</th>
            <th class="px-4 py-2 border">Biaya Taxi Berangkat</th>
            <th class="px-4 py-2 border">Biaya Taxi Kembali</th>
            <th class="px-4 py-2 border">Biaya Travel</th>
            <th class="px-4 py-2 border">Tidak Menginap di Hotel</th>
            <th class="px-4 py-2 border">Total</th>
            <th class="px-4 py-2 border">Bukti Lampiran</th>
            <th class="px-4 py-2 border">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(cost, i) in cost"
            :key="cost.id"
            class="odd:bg-white even:bg-gray-50"
          >
            <td class="px-4 py-2 border">{{ getRowNumber(i) }}</td>
            <td class="px-4 py-2 border">
             {{ cost.pegawai?.nama_pegawai ?? '-' }}
            </td>
            <td class="px-4 py-2 border">{{ cost.nomor_surat ?? '-' }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.uang_perhari) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.total_uang_harian) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.uang_representasi) }}</td>
            <td class="px-4 py-2 border">{{ cost.nama_hotel }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_akomodasi) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_kontribusi) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_tiket_berangkat) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_tiket_kembali) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_bantuan_transport) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_taxi_berangkat) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_taxi_kembali) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_travel) }}</td>
            <td class="px-4 py-2 border">{{ formatRupiah(cost.biaya_tidak_menginap) }}</td>
            <td class="px-4 py-2 border font-bold">{{ formatRupiah(cost.total_biaya) }}</td>
            <td class="px-4 py-2 border text-center">
              <template v-if="cost.bukti_file_url">
                <span class="block text-xs text-green-600 mb-1">Sudah upload</span>
                <div class="flex gap-1 justify-center">
                  <!-- Lihat -->
                  <a
                    :href="cost.bukti_file_url"
                    target="_blank"
                    class="w-7 h-7 flex items-center justify-center rounded-full bg-yellow-500 text-white hover:bg-yellow-600"
                    title="Lihat"
                  >
                    <i class="fa fa-eye text-xs"></i>
                  </a>
                  <!-- Download -->
                  <!-- <a
                    :href="cost.bukti_file_url"
                    download
                    class="w-7 h-7 flex items-center justify-center rounded-full bg-green-500 text-white hover:bg-green-600"
                    title="Download"
                  >
                    <i class="fa fa-download text-xs"></i>
                  </a> -->
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
                    :to="`/admin/rincian-biaya/edit/${cost.id}`"
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
          <tr v-if="!cost.length">
            <td colspan="18" class="text-center py-4 text-gray-500">
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
import { ref, onMounted,watch } from "vue"
import axios from "axios"

const cost = ref([])
const currentPage = ref(1)
const search = ref("")
const totalPages = ref(1)

const perPage = 10

const fetchCosts = async () => {
  try {
    const res = await axios.get("/api/costs", {
        params: {
          page: currentPage.value,
          per_page: perPage,
          search: search.value
        }
    });

    cost.value = Array.isArray(res.data?.data) ? res.data.data : [];
    currentPage.value = Number(res.data?.meta?.current_page) || 1;
    totalPages.value = Number(res.data?.meta?.last_page) || 1;
  } catch (e) {
    console.error("DETAIL ERROR:", e.response?.data || e.message);
    cost.value = [];
    currentPage.value = 1;
    totalPages.value = 1;
  }
}

const getRowNumber = (index) => ((currentPage.value - 1) * perPage) + index + 1

const formatRupiah = (value) => {
  if (!value) return "Rp 0"
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(value)
}

watch(search, () => {
  currentPage.value = 1
  fetchCosts()
})

watch(currentPage, () => {
  fetchCosts()
})

onMounted(fetchCosts)
</script>
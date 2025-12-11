<template>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Data SPPD</h1>
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
        <thead class="bg-gray-200 text-left">
          <tr>
            <th class="px-4 py-2 border">No</th>
            <th class="px-4 py-2 border">Nama Pegawai</th>
            <th class="px-4 py-2 border">Nomor Surat</th>
            <th class="px-4 py-2 border">Daerah</th>
            <th class="px-4 py-2 border">Tujuan</th>
            <th class="px-4 py-2 border">Moda Transportasi</th>
            <th class="px-4 py-2 border">Tanggal Berangkat</th>
            <th class="px-4 py-2 border">Tanggal Kembali</th>
            <th class="px-4 py-2 border">Lama Hari</th>
            <th class="px-4 py-2 border">Status</th>
            <th class="px-4 py-2 border">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(sppd, i) in sppds"
            :key="sppd.id"
            class="odd:bg-white even:bg-gray-50"
          >
            <td class="px-4 py-2 border">{{ getRowNumber(i) }}</td>
            <td class="px-4 py-2 border">
              <ul>
                <li v-for="pegawai in sppd.spt?.employees" :key="pegawai.id">
                  {{ pegawai.nama_pegawai }}
                </li>
              </ul>
            </td>
            <td class="px-4 py-2 border">{{ sppd.spt?.nomor_surat }}</td>
            <td class="px-4 py-2 border">{{ sppd.daerah }}</td>
            <td class="px-4 py-2 border">{{ sppd.tujuan }}</td>
            <td class="px-4 py-2 border">
              {{ Array.isArray(sppd.moda_transport) ? sppd.moda_transport.join(', ') : sppd.moda_transport }}
            </td>
            <td class="px-4 py-2 border">{{ sppd.tanggal_berangkat }}</td>
            <td class="px-4 py-2 border">{{ sppd.tanggal_kembali }}</td>
            <td class="px-4 py-2 border">{{ sppd.lama_hari }}</td>
            <td class="px-4 py-2 border">
              <span
                v-if="getStatus(sppd) === 'Berangkat'"
                class="px-2 py-1 rounded text-white bg-green-500"
              >
                Berangkat
              </span>
              <span
                v-else-if="getStatus(sppd) === 'Pulang'"
                class="px-2 py-1 rounded text-white bg-red-500"
              >
                Pulang
              </span>
              <span
                v-else-if="getStatus(sppd) === 'Sedang Dinas'"
                class="px-2 py-1 rounded text-black bg-yellow-400"
              >
                Sedang Dinas
              </span>
              <span
                v-else-if="getStatus(sppd) === 'Selesai'"
                class="px-2 py-1 rounded text-white bg-amber-950"
              >
                Selesai
              </span>
              <span
                v-else
                class="px-2 py-1 rounded text-white bg-purple-500"
              >
                Belum Berangkat
              </span>
            </td>
            <td class="px-4 py-2 border">
              <div class="flex gap-1">
                <div class="relative group">
                  <router-link
                    :to="`/user/sppd/edit/${sppd.id}`"
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
          <tr v-if="sppds.length === 0">
            <td colspan="11" class="text-center py-4">Tidak ada data</td>
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
import { ref, onMounted, watch } from "vue";
import axios from "axios";

const sppds = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const search = ref("");

const perPage = 10

const fetchDataSppd = async () => {
    try {
      const res = await axios.get('/api/sppds', {
        params: {
          page: currentPage.value,
          per_page: perPage,
          search: search.value
        }
      });
      sppds.value = Array.isArray(res.data?.data) ? res.data.data : [];
      currentPage.value = Number(res.data?.meta?.current_page) || 1;
      totalPages.value = Number(res.data?.meta?.last_page) || 1;
    } catch (e) {
      console.error("DETAIL ERROR:", e.response?.data);
      sppds.value = [];       
      totalPages.value = 1;
    }
  };

const getRowNumber = (index) => ((currentPage.value - 1) * perPage) + index + 1

function getStatus(sppd) {
  const today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
  const berangkat = sppd.tanggal_berangkat;
  const kembali = sppd.tanggal_kembali;

  if (!berangkat) return "Belum Berangkat";
  if (today === berangkat) return "Berangkat";
  if (today === kembali) return "Pulang";
  if (today > berangkat && today < kembali) return "Sedang Dinas";
  if (today > kembali) return "Selesai";

  return "Belum Berangkat";
}

watch(search, () => {
  currentPage.value = 1
  fetchDataSppd()
})

watch(currentPage, () => {
  fetchDataSppd()
})

onMounted(fetchDataSppd)
</script>
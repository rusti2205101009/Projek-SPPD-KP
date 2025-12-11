<template>
    <h1 class="text-2xl font-bold mb-6">Data Surat Perintah Tugas</h1>
      <div class="flex items-center gap-4 mb-6">
        <input
          v-model="search"
          type="text"
          placeholder="--cari disini--"
          class="border rounded px-3 py-2 flex-1"
        />

        <router-link
          to="/admin/spt/create"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Tambah Data
        </router-link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full border border-gray-300 rounded-lg overflow-hidden text-sm table-auto">
          <thead class="bg-gray-200 text-center">
            <tr>
              <th class="px-4 py-2 border">No</th>
              <th class="px-4 py-2 border">Nama Pegawai</th>
              <th class="px-4 py-2 border">No Surat</th>
              <th class="px-4 py-2 border">NIP/NIPPPK</th>
              <th class="px-4 py-2 border">Pangkat</th>
              <th class="px-4 py-2 border">Golongan</th>
              <th class="px-4 py-2 border">Jabatan</th>
              <th class="px-4 py-2 border">Tanggal Surat</th>
              <th class="px-4 py-2 border">Keperluan Perjalanan Dinas</th>
              <th class="px-4 py-2 border">Bidang/Unit</th>
              <th class="px-4 py-2 border">Nama Kepala Bagian</th>
              <th class="px-4 py-2 border">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(spt, i) in spts"
              :key="spt.id"
              :class="i % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
            >
              <td class="px-4 py-2 border">{{ getRowNumber(i) }}</td>

              <td class="px-4 py-2 border">
                <ul class="list-disc pl-4">
                  <li v-for="(pegawai, idx) in spt.employees" :key="idx">
                    {{ pegawai.nama_pegawai }}
                  </li>
                </ul>
              </td>

              <td class="px-4 py-2 border">{{ spt.nomor_surat }}</td>

              <td class="px-4 py-2 border">
                <ul class="list-disc pl-4">
                  <li v-for="(pegawai, idx) in spt.employees" :key="idx">
                    {{ pegawai.nip_nipppk }}
                  </li>
                </ul>
              </td>

              <td class="px-4 py-2 border">
                <ul class="list-disc pl-4">
                  <li v-for="(pegawai, idx) in spt.employees" :key="idx">
                    {{ pegawai.pangkat }}
                  </li>
                </ul>
              </td>

              <td class="px-4 py-2 border">
                <ul class="list-disc pl-4">
                  <li v-for="(pegawai, idx) in spt.employees" :key="idx">
                    {{ pegawai.golongan }}
                  </li>
                </ul>
              </td>

             <td class="px-4 py-2 border">
              <ul class="list-disc pl-4">
                <li v-for="(pegawai, idx) in spt.employees" :key="idx">
                  {{ pegawai.jabatan }}
                </li>
              </ul>
            </td>

            <td class="px-4 py-2 border">
              {{ new Date(spt.tanggal_surat).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) }}
            </td>

            <td class="px-4 py-2 border">{{ spt.keperluan }}</td>

            <td class="px-4 py-2 border">
              <ul class="list-disc pl-4">
                <li v-for="(pegawai, idx) in spt.employees" :key="idx">
                  {{ pegawai.bidang }}
                </li>
              </ul>
            </td>

            <td class="px-4 py-2 border">{{ spt.head_division ? spt.head_division.nama_kepala : '-' }}</td>
            <td class="px-4 py-2 border">
              <div class="flex gap-1">
                <div class="relative group">
                  <router-link
                    :to="`/admin/spt/edit/${spt.id}`"
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
                    @click="printItem(spt.id)"
                    class="w-7 h-7 flex items-center justify-center rounded-full bg-purple-600 text-white hover:bg-purple-700"
                  >
                    <i class="fa fa-print text-xs"></i>
                  </button>
                  <span
                    class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 text-xs text-white bg-gray-800 rounded opacity-0 group-hover:opacity-100 transition pointer-events-none"
                  >
                    Print
                  </span>
                </div>
                <div class="relative group">
                  <button
                    @click="deleteItem(spt.id)"
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
            <tr v-if="!spts || spts.length === 0">
              <td class="px-4 py-4 border text-center" colspan="12">
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
  import axios from 'axios'
  import { useRouter } from 'vue-router'

  const router = useRouter()

  const spts = ref([])
  const currentPage = ref(1)
  const search = ref("")
  const totalPages = ref(1)

  const perPage = 10

  const fetchDataSpt = async () => {
    try {
      const res = await axios.get('/api/spts', {
        params: {
          page: currentPage.value,
          per_page: perPage,
          search: search.value
        }
      });
      // console.log("API RESPONSE:", res.data) 

      spts.value = Array.isArray(res.data?.data) ? res.data.data : [];
      currentPage.value = Number(res.data?.meta?.current_page) || 1;
      totalPages.value = Number(res.data?.meta?.last_page) || 1;
    } catch (e) {
      console.error("DETAIL ERROR:", e.response?.data);
    }
  };

const getRowNumber = (index) => ((currentPage.value - 1) * perPage) + index + 1

watch(search, () => {
  currentPage.value = 1
  fetchDataSpt()
})

watch(currentPage, () => {
  fetchDataSpt()
})

onMounted(fetchDataSpt)

const deleteItem = async (id) => {
  if (confirm("Yakin ingin hapus data ini?")) {
    try {
      await axios.delete(`/api/spts/${id}`)
      alert("Data berhasil dihapus")
      fetchDataSpt()
    } catch (e) {
      alert("Gagal menghapus data: " + e.message)
    }
  }
}

const printItem = async (id) => {
  try {
    const res = await axios.get(`/api/spts/${id}/cetak`, {
      withCredentials: true,
      responseType: "blob"
    });

    const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `spt_${id}.docx`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error(error.response?.data || error.message);
    alert("Gagal cetak SPT");
  }
};

</script>
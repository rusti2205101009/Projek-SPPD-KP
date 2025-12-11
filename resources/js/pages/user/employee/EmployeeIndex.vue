<template>
  <div class=" bg-gray-50 min-h-screen">
    <div class="mb-6">
      <h1 class="text-2xl font-bold">Data Pegawai</h1>
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
        <thead class="bg-gray-200 text-center">
          <tr>
            <th class="px-4 py-2 border">No</th>
            <th class="px-4 py-2 border">Foto</th>
            <th class="px-4 py-2 border">NIP/NIPPPK</th>
            <th class="px-4 py-2 border">Nama</th>
            <th class="px-4 py-2 border">Pangkat</th>
            <th class="px-4 py-2 border">Golongan</th>
            <th class="px-4 py-2 border">Jabatan</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(employee, index) in employees"
            :key="employee.id"
            class="hover:bg-gray-50"
          >
            <!-- <td class="border px-4 py-2 text-center">{{ index + 1 }}</td> -->
            <td class="border px-4 py-2 text-center">{{ getRowNumber(index) }}</td>
            <td class="border px-4 py-2 text-center">
                <img
                  :src="employee.foto || defaultAvatar"
                  alt="Foto Pegawai"
                  class="w-12 h-12 object-cover rounded"
                />
              </td>
            <td class="border px-4 py-2">{{ employee.nip_nipppk }}</td>
            <td class="border px-4 py-2">{{ formatNama(employee) }}</td>
            <td class="border px-4 py-2">{{ employee.pangkat }}</td>
            <td class="border px-4 py-2">{{ employee.golongan }}</td>
            <td class="border px-4 py-2">{{ employee.jabatan }}</td>
          </tr>
          <tr v-if="employees.length === 0">
            <td colspan="9" class="border px-4 py-2 text-center text-gray-500">
              Tidak ada data
            </td>
          </tr>
        </tbody>
      </table>
    </div>
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

const employees = ref([]);
const search = ref("");
const currentPage = ref(1);
const totalPages = ref(1);

const perPage = 10

const loadEmployees = async () => {
  try {
    const res = await axios.get("/api/employees", {
      params: {
        per_page: perPage,
        search: search.value,
        page: currentPage.value, 
      },
    });
      employees.value = Array.isArray(res.data?.data) ? res.data.data : [];
      currentPage.value = Number(res.data?.meta?.current_page) || 1;
      totalPages.value = Number(res.data?.meta?.last_page) || 1;
  } catch (error) {
    alert("Gagal memuat data: " + error.message);
  }
};

const getRowNumber = (index) => ((currentPage.value - 1) * perPage) + index + 1

const formatNama = (employee) => {
  const depan = employee.gelar_depan ? employee.gelar_depan.trim() + " " : "";
  const belakang = employee.gelar_belakang
    ? ", " + employee.gelar_belakang.trim()
    : "";
  const nama = employee.nama_pegawai || "";
  return `${depan}${nama}${belakang}`;
};

watch(search, () => {
    currentPage.value = 1
    loadEmployees()
  })

  watch(currentPage, () => {
    loadEmployees()
  })

  onMounted(loadEmployees)
</script>
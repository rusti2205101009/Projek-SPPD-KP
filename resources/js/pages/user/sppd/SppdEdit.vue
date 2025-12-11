<template>
  <main class="flex-1 p-6 overflow-y-auto">
    <router-link
      to="/user/sppd"
      class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
    >
      Kembali
    </router-link>

    <h2 class="text-xl font-bold mb-4">Edit Surat Perintah Perjalanan Dinas</h2>

    <form
      @submit.prevent="updateSppd"
      class="bg-white p-6 rounded-lg shadow-md grid grid-cols-1 md:grid-cols-2 gap-4"
    >
      <div class="col-span-1 md:col-span-2">
        <label class="block mb-1 font-semibold">Pegawai</label>
        <textarea 
          readonly 
          class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed h-24 resize-none"
        >{{ employees.map(p => p.nama_pegawai).join('\n') }}</textarea>
      </div>

      <div>
        <label class="block mb-1 font-semibold">Nomor Surat</label>
        <input 
          v-model="sppd.nomor_surat" 
          type="text" 
          class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
          readonly
        >
      </div>

      <div>
        <label class="block mb-1 font-semibold">Moda Transportasi</label>
        <Multiselect
          v-model="sppd.moda_transport"
          :options="['Darat', 'Udara', 'Air']"
          :multiple="true"
          :close-on-select="false"
          placeholder="Pilih Moda Transport"
          class="w-full"
        />
        <p class="text-sm text-gray-500 mt-1">Bisa memilih lebih dari satu moda transportasi.</p>
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tanggal Berangkat</label>
        <input v-model="sppd.tanggal_berangkat" type="date" class="w-full border rounded px-3 py-2">
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tanggal Kembali</label>
        <input v-model="sppd.tanggal_kembali" type="date" class="w-full border rounded px-3 py-2">
      </div>

      <div>
        <label class="block mb-1 font-semibold">Daerah</label>
        <input v-model="sppd.daerah" type="text" class="w-full border rounded px-3 py-2">
      </div>

      <div>
        <label class="block mb-1 font-semibold">Lama Hari</label>
        <input 
          :value="lamaHari" 
          type="number" 
          class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
          readonly
        >
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tujuan</label>
        <input v-model="sppd.tujuan" type="text" class="w-full border rounded px-3 py-2">
      </div>

      <div class="col-span-1 md:col-span-2 text-right">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">
          Update
        </button>
      </div>
    </form>
  </main>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import Multiselect from 'vue-multiselect'

const route = useRoute();
const router = useRouter();

const sppd = ref({
  spt_id: null,
  nomor_surat: "",
  daerah: "",
  tujuan: "",
  moda_transport: [],
  lama_hari: null,
  pegawai_ids: [],
});

const employees = ref([]);

// const transportOptions = {
//   Darat: ["Mobil Pribadi", "Mobil Dinas", "Bus", "Kereta", "Travel"],
//   Udara: ["Pesawat"],
//   Air: ["Kapal"],
// };

// const availableTransport = computed(() => {
//    return sppd.value.moda_transport.flatMap(moda => transportOptions[moda] || []);
// });

// watch(() => sppd.value.moda_transport, (newModa) => {
//   if (!transportOptions[newModa]?.includes(sppd.value.transportasi)) {
//     sppd.value.transportasi = "";
//   }
// });

const lamaHari = computed(() => {
  if (sppd.value.tanggal_berangkat && sppd.value.tanggal_kembali) {
    const start = new Date(sppd.value.tanggal_berangkat)
    const end = new Date(sppd.value.tanggal_kembali)
    const diff = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1
    return diff > 0 ? diff : 0
  }
  return 0
})

const fetchDetail = async () => {
  try {
    const res = await axios.get(`/api/sppds/${route.params.id}`);
    const data = res.data.data;

    sppd.value = {
      spt_id: data.spt?.id || null,
      nomor_surat: data.spt?.nomor_surat || "",
      daerah: data.daerah || "",
      tujuan: data.tujuan || "",
      moda_transport: Array.isArray(data.moda_transport)
        ? data.moda_transport
        : data.moda_transport
        ? data.moda_transport.split(",").map(item => item.trim())
        : [],
      tanggal_berangkat: data.tanggal_berangkat || "",
      tanggal_kembali: data.tanggal_kembali || "",
      pegawai_ids: data.spt?.employees?.map(e => e.id) || [],
    };

    employees.value = data.spt?.employees || [];
  } catch (error) {
    console.error("Gagal ambil data Surat Perintah Perjalanan Dinas", error.response?.data || error.message);
  }
};

const updateSppd = async () => {
  try {
    const payload = { ...sppd.value };

    delete payload.nomor_surat;
    delete payload.pegawai_ids;
    delete payload.lama_hari;

    const res = await axios.put(`/api/sppds/${route.params.id}`, payload);
    alert(res.data.message || "Berhasil update Surat Perintah Perjalanan Dinas");
    router.push("/user/sppd");
  } catch (error) {
    console.error(error.response?.data || error.message);
    alert("Gagal update Surat Perintah Perjalanan Dinas");
  }
};

onMounted(fetchDetail);
</script>
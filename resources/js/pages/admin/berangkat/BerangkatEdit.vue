<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Edit Keberangkatan</h1>
    </div>

    <router-link
      to="/admin/keberangkatan"
      class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
    >
      Kembali
    </router-link>

    <form
      @submit.prevent="updateDeparture"
      class="bg-white p-6 rounded-lg shadow-md grid grid-cols-1 md:grid-cols-2 gap-4"
    >
      <div>
        <label class="block mb-1 font-semibold">Nama Pegawai</label>
        <input
          v-model="form.nama_pegawai"
          type="text"
          class="w-full border rounded px-3 py-2 bg-gray-100"
          readonly
        />
      </div>
      
      <div>
        <label class="block mb-1 font-semibold">Nomor Surat</label>
        <input
          :value="form.nomor_surat"
          type="text"
          class="w-full border rounded px-3 py-2 bg-gray-100"
          readonly
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Nama Maskapai/Kereta/Travel/Bis</label>
        <input v-model="form.nama_transportasi" type="text" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tempat Asal</label>
        <input v-model="form.tempat_asal" type="text" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Nomor Bukti</label>
        <input v-model="form.no_bukti" type="text" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Daerah Tujuan</label>
        <input v-model="form.daerah_tujuan" type="text" class="w-full border rounded px-3 py-2" readonly />
      </div>  

      <div>
        <label class="block mb-1 font-semibold">Tempat Tujuan</label>
        <input v-model="form.tempat_tujuan" type="text" class="w-full border rounded px-3 py-2" readonly />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tanggal Bukti</label>
        <input v-model="form.tanggal_bukti" type="date" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tanggal Berangkat</label>
        <input v-model="form.tanggal_berangkat_tiket" type="date" class="w-full border rounded px-3 py-2" readonly />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Nomor Tiket</label>
        <input v-model="form.no_tiket_berangkat" type="text" class="w-full border rounded px-3 py-2" />
      </div>

      <div class="col-span-1 md:col-span-2">
        <label class="block mb-1 font-semibold">Upload Bukti (Semua Lampiran Dijadikan Satu File Pdf Maks 10mb)</label>
        <input
          type="file"
          accept="application/pdf"
          @change="onFileChange"
          class="w-full border rounded px-3 py-2"
        />
        <p v-if="form.bukti_file_berangkat_url" class="text-sm mt-1">
          File saat ini:
          <a :href="form.bukti_file_berangkat_url" target="_blank" class="text-blue-600 underline">
            Lihat Bukti PDF
          </a>
        </p>
      </div>

      <div class="col-span-1 md:col-span-2 text-right">
        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold"
        >
          Simpan
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"
import axios from "axios"

const route = useRoute()
const router = useRouter()

const buktiFileBerangkat = ref(null);

const onFileChange = (event) => {
  buktiFileBerangkat.value = event.target.files[0];
};

const form = ref({
  nama_pegawai: "",
  nomor_surat: "",
  nama_transportasi: "",
  tempat_asal: "",
  no_bukti: "",
  daerah_tujuan: "",
  tempat_tujuan: "",
  tanggal_bukti: "",
  tanggal_berangkat_tiket: "",
  no_tiket_berangkat: "",
  bukti_file_berangkat_url: null,
})

const fetchDeparture = async () => {
  try {
    const res = await axios.get(`/api/departures/${route.params.id}`);
    const data = res.data.data;

    form.value.nama_pegawai = data.pegawai?.nama_pegawai ?? "";
    form.value.nomor_surat = data.nomor_surat ?? "";
    form.value.no_bukti = data.no_bukti ?? "";
    form.value.tanggal_bukti = data.tanggal_bukti ?? "";
    form.value.no_tiket_berangkat = data.no_tiket_berangkat ?? "";
    form.value.nama_transportasi = data.nama_transportasi ?? "";
    form.value.tempat_asal = data.tempat_asal ?? "";
    form.value.daerah_tujuan = data.daerah_tujuan ?? "";
    form.value.tempat_tujuan = data.tempat_tujuan ?? "";
    form.value.tanggal_berangkat_tiket = data.tanggal_berangkat_tiket ?? "";
    form.value.bukti_file_berangkat_url = data.bukti_file_berangkat_url ?? null;
  } catch (error) {
    console.error("Gagal ambil data departure", error.response?.data || error.message);
  }
};

// const updateDeparture = async () => {
//   try {
//     const payload = { ...form.value }
//     delete payload.nama_pegawai
//     delete payload.nomor_surat
//     delete payload.daerah_tujuan
//     delete payload.tempat_tujuan

//     await axios.put(`/api/departures/${route.params.id}`, payload)
//     alert("Data berhasil diperbarui")
//     router.push("/admin/keberangkatan")
//   } catch (e) {
//     alert("Gagal update data: " + e.message)
//   }
// }

const updateDeparture = async () => {
  try {
    const payload = new FormData();
    payload.append('nama_transportasi', form.value.nama_transportasi);
    payload.append('tempat_asal', form.value.tempat_asal);
    payload.append('no_bukti', form.value.no_bukti);
    payload.append('tanggal_bukti', form.value.tanggal_bukti);
    payload.append('tanggal_berangkat_tiket', form.value.tanggal_berangkat_tiket);
    payload.append('no_tiket_berangkat', form.value.no_tiket_berangkat);

    if (buktiFileBerangkat.value) {
      payload.append('bukti_file_berangkat', buktiFileBerangkat.value);
    }

    payload.append('_method', 'PUT');

    await axios.post(`/api/departures/${route.params.id}`, payload, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    alert("Data berhasil diperbarui");
    router.push("/admin/keberangkatan");
  } catch (e) {
    alert("Gagal update data: " + (e.response?.data?.message || e.message));
  }
};

onMounted(() => {
  fetchDeparture();
});
</script>
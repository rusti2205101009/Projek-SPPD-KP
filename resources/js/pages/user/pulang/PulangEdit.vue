<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Edit Kepulangan</h1>
    </div>

    <router-link
      to="/user/kepulangan"
      class="mb-6 px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 font-bold inline-block"
    >
      Kembali
    </router-link>

    <form
      @submit.prevent="updateReturn"
      class="bg-white p-6 rounded-lg shadow-md grid grid-cols-1 md:grid-cols-2 gap-4"
    >
      <div>
        <label class="block mb-1 font-semibold">Nama Pegawai</label>
        <input
          :value="form.nama_pegawai"
          type="text"
          class="w-full border rounded px-3 py-2 bg-gray-100"
          readonly
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Nama Maskapai/Kereta/Travel/Bis</label>
        <input
          v-model="form.nama_transportasi"
          type="text"
          class="w-full border rounded px-3 py-2"
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
        <label class="block mb-1 font-semibold">Daerah Asal</label>
        <input
          v-model="form.daerah_asal"
          type="text"
          class="w-full border rounded px-3 py-2"
          readonly
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tempat Asal</label>
        <input
          v-model="form.tempat_asal"
          type="text"
          class="w-full border rounded px-3 py-2"
          readonly
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Nomor Bukti</label>
        <input
          v-model="form.no_bukti"
          type="text"
          class="w-full border rounded px-3 py-2"
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tempat Tujuan</label>
        <input
          v-model="form.tempat_tujuan"
          type="text"
          class="w-full border rounded px-3 py-2 bg-white"
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tanggal Bukti</label>
        <input
          v-model="form.tanggal_bukti"
          type="date"
          class="w-full border rounded px-3 py-2"
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tanggal Pulang Tiket</label>
        <input
          v-model="form.tanggal_kembali_tiket"
          type="date"
          class="w-full border rounded px-3 py-2"
          readonly
        />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Nomor Tiket</label>
        <input
          v-model="form.no_tiket_kembali"
          type="text"
          class="w-full border rounded px-3 py-2"
        />
      </div>

      <div class="col-span-1 md:col-span-2">
        <label class="block mb-1 font-semibold">Upload Bukti (Semua Lampiran Dijadikan Satu File Pdf Maks 10mb)</label>
        <input
          type="file"
          accept="application/pdf"
          @change="onFileChange"
          class="w-full border rounded px-3 py-2"
        />
        <p v-if="form.bukti_file_pulang_url" class="text-sm mt-1">
          File saat ini:
          <a :href="form.bukti_file_pulang_url" target="_blank" class="text-blue-600 underline">
            Lihat Bukti PDF
          </a>
        </p>
      </div>

      <div class="col-span-1 md:col-span-2 text-right">
        <button
          type="submit"
          class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold"
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

const buktiFilePulang = ref(null);

const onFileChange = (event) => {
  buktiFilePulang.value = event.target.files[0];
};

const form = ref({
  gelar_depan: "",
  nama_pegawai: "",
  gelar_belakang: "",
  nomor_surat: "",
  no_bukti: "",
  tanggal_bukti: "",
  no_tiket_kembali: "",
  tanggal_kembali_tiket: "",
  nama_transportasi: "",
  tempat_asal: "",
  daerah_asal: "",
  tempat_tujuan: "",
  bukti_file_pulang_url: null,
})

const fetchReturn = async () => {
  try {
    const res = await axios.get(`/api/returns/${route.params.id}`);
    const data = res.data.data;

    form.value.nama_pegawai = data.pegawai?.nama_pegawai ?? "";
    form.value.nomor_surat = data.nomor_surat ?? "";
    form.value.no_bukti = data.no_bukti ?? "";
    form.value.tanggal_bukti = data.tanggal_bukti ?? "";
    form.value.no_tiket_kembali = data.no_tiket_kembali ?? "";
    form.value.nama_transportasi = data.nama_transportasi ?? "";
    form.value.tempat_asal = data.tempat_asal ?? "";
    form.value.daerah_asal = data.daerah_asal ?? "";
    form.value.tempat_tujuan = data.tempat_tujuan ?? "";
    form.value.tanggal_kembali_tiket = data.tanggal_kembali_tiket ?? "";
    form.value.bukti_file_pulang_url = data.bukti_file_pulang_url ?? null;
  } catch (error) {
    console.error("Gagal ambil data departure", error.response?.data || error.message);
  }
};

const updateReturn = async () => {
  try {
    const fd = new FormData();
    
    fd.append('no_bukti', form.value.no_bukti || '');
    fd.append('tanggal_bukti', form.value.tanggal_bukti || '');
    fd.append('no_tiket_kembali', form.value.no_tiket_kembali || '');
    fd.append('tanggal_kembali_tiket', form.value.tanggal_kembali_tiket || '');
    fd.append('nama_transportasi', form.value.nama_transportasi || '');
    fd.append('tempat_tujuan', form.value.tempat_tujuan || '');

    if (buktiFilePulang.value) {
      fd.append('bukti_file_pulang', buktiFilePulang.value);
    }

    await axios.post(`/api/returns/${route.params.id}?_method=PUT`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    alert("Data berhasil diperbarui");
    router.push("/user/kepulangan");
  } catch (e) {
    alert("Gagal update data: " + (e.response?.data?.message || e.message));
  }
};

onMounted(() => {
  fetchReturn();
});
</script>
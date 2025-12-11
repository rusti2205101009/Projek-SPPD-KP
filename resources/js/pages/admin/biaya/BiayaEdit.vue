<template>
  <main class="flex-1 p-6 overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Edit Rincian Biaya</h1>
    </div>

    <router-link
      to="/admin/rincian-biaya"
      class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
    >
      Kembali
    </router-link>

    <form
      @submit.prevent="updateBiaya"
      class="bg-white p-6 rounded-lg shadow-md grid grid-cols-1 md:grid-cols-2 gap-4"
    >
      <div>
        <label class="block mb-1 font-semibold">Nama Pegawai</label>
         <input 
          v-model="cost.nama_pegawai"
          type="text" 
          readonly 
          class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
        />
    </div>

    <div>
        <label class="block mb-1 font-semibold">Nomor Surat</label>
        <input 
          v-model="cost.nomor_surat" 
          type="text" 
          class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed" 
          readonly
        >
      </div>

      <div>
        <label class="block mb-1 font-semibold">Uang Perhari</label>
        <input v-model.number="cost.uang_perhari" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Total Perhari</label>
        <input :value="totalPerhari" type="number" class="w-full border rounded px-3 py-2" readonly />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Uang Representasi</label>
        <input v-model.number="cost.uang_representasi" type="number" class="w-full border rounded px-3 py-2" placeholder="Khusus Sekda, Pimpinan dan Anggota DPRD, dan Pejabat Es II" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Nama Penginapan/Hotel</label>
        <input v-model="cost.nama_hotel" type="text" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Biaya Akomodasi</label>
        <input v-model.number="cost.biaya_akomodasi" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Biaya Lain/Kontribusi</label>
        <input v-model.number="cost.biaya_kontribusi" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Biaya Tiket Berangkat</label>
        <input v-model.number="cost.biaya_tiket_berangkat" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Biaya Tiket Kembali</label>
        <input v-model.number="cost.biaya_tiket_kembali" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Bantuan Transport/BBM</label>
        <input v-model.number="cost.biaya_bantuan_transport" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Biaya Taxi Berangkat</label>
        <input v-model.number="cost.biaya_taxi_berangkat" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Biaya Taxi Kembali</label>
        <input v-model.number="cost.biaya_taxi_kembali" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Biaya Travel</label>
        <input v-model.number="cost.biaya_travel" type="number" class="w-full border rounded px-3 py-2" />
      </div>

      <div>
        <label class="block mb-1 font-semibold">Tidak Menginap di Hotel (30%)</label>
        <input v-model.number="cost.biaya_tidak_menginap" type="number" class="w-full border rounded px-3 py-2" />
      </div>
      
      <div>
        <label class="block mb-1 font-semibold">Total Biaya</label>
        <input :value="totalBiaya" type="number" class="w-full border rounded px-3 py-2" readonly />
      </div>

      <div class="col-span-1 md:col-span-2">
        <label class="block mb-1 font-semibold">Upload Bukti (Semua Lampiran Dijadikan Satu File Pdf Maks 10mb)</label>
        <input
          type="file"
          accept="application/pdf"
          @change="onFileChange"
          class="w-full border rounded px-3 py-2"
        />
        <p v-if="cost.bukti_file_url" class="text-sm mt-1">
          File saat ini:
          <a :href="cost.bukti_file_url" target="_blank" class="text-blue-600 underline">
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
  </main>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();
const lamaHari = ref(0);

const buktiFile = ref(null);

const onFileChange = (event) => {
  buktiFile.value = event.target.files[0];
};

const cost = reactive({
  employee_id: null,
  nama_pegawai: "",
  nomor_surat: "",
  uang_perhari: 0,
  uang_representasi: 0,
  nama_hotel: "",
  biaya_kontribusi: 0,
  biaya_akomodasi: 0,
  biaya_tiket_berangkat: 0,
  biaya_tiket_kembali: 0,
  biaya_bantuan_transport: 0,
  biaya_taxi_berangkat: 0,
  biaya_taxi_kembali: 0,
  biaya_travel: 0,
  biaya_tidak_menginap: 0,
  bukti_file_url: null,
});

const totalPerhari = computed(() => (cost.uang_perhari || 0) * (lamaHari.value || 0));

const totalBiaya = computed(() =>
  (totalPerhari.value || 0) +
  (cost.uang_representasi || 0) +
  (cost.biaya_akomodasi || 0) +
  (cost.biaya_kontribusi || 0) +
  (cost.biaya_tiket_berangkat || 0) +
  (cost.biaya_tiket_kembali || 0) +
  (cost.biaya_bantuan_transport || 0) +
  (cost.biaya_taxi_berangkat || 0) +
  (cost.biaya_taxi_kembali || 0) +
  (cost.biaya_travel || 0) +
  (cost.biaya_tidak_menginap || 0)
);

const fetchCost = async () => {
  try {
    const res = await axios.get(`/api/costs/${route.params.id}`);
    const data = res.data.data;

    cost.employee_id = data.pegawai?.id ?? null;
    cost.nama_pegawai = data.pegawai?.nama_pegawai ?? "";
    cost.nomor_surat = data.nomor_surat ?? "";
    cost.uang_perhari = Number(data.uang_perhari) || 0;
    cost.uang_representasi = Number(data.uang_representasi) || 0;
    cost.nama_hotel = data.nama_hotel ?? "";
    cost.biaya_kontribusi = Number(data.biaya_kontribusi) || 0;
    cost.biaya_akomodasi = Number(data.biaya_akomodasi) || 0;
    cost.biaya_tiket_berangkat = Number(data.biaya_tiket_berangkat) || 0;
    cost.biaya_tiket_kembali = Number(data.biaya_tiket_kembali) || 0;
    cost.biaya_bantuan_transport = Number(data.biaya_bantuan_transport) || 0;
    cost.biaya_taxi_berangkat = Number(data.biaya_taxi_berangkat) || 0;
    cost.biaya_taxi_kembali = Number(data.biaya_taxi_kembali) || 0;
    cost.biaya_travel = Number(data.biaya_travel) || 0;
    cost.biaya_tidak_menginap = Number(data.biaya_tidak_menginap) || 0;
    cost.bukti_file_url = data.bukti_file_url ?? null;

    lamaHari.value = data.lama_hari ?? 0;
  } catch (error) {
    console.error("Gagal ambil data rincian biaya", error.response?.data || error.message);
  }
};

// const updateBiaya = async () => {
//   try {
//     const payload = {
//       uang_perhari: cost.uang_perhari,
//       uang_representasi: cost.uang_representasi,
//       nama_hotel: cost.nama_hotel,
//       biaya_kontribusi: cost.biaya_kontribusi,
//       biaya_akomodasi: cost.biaya_akomodasi,
//       biaya_tiket_berangkat: cost.biaya_tiket_berangkat,
//       biaya_tiket_kembali: cost.biaya_tiket_kembali,
//       biaya_bantuan_transport: cost.biaya_bantuan_transport,
//       biaya_taxi_berangkat: cost.biaya_taxi_berangkat,
//       biaya_taxi_kembali: cost.biaya_taxi_kembali,
//       biaya_travel: cost.biaya_travel,
//       biaya_tidak_menginap: cost.biaya_tidak_menginap,
//       lama_hari: lamaHari.value,
//       total_uang_harian: totalPerhari.value,
//       total_biaya: totalBiaya.value,
//     };

//     const res = await axios.put(`/api/costs/${route.params.id}`, payload);
//     alert(res.data.message || "Data berhasil diupdate");
//     router.push("/admin/rincian-biaya");
//   } catch (error) {
//     console.error(error.response?.data || error.message);
//     alert("Gagal update rincian biaya");
//   }
// };

const updateBiaya = async () => {
  try {
    const formData = new FormData();
    formData.append("uang_perhari", cost.uang_perhari);
    formData.append("uang_representasi", cost.uang_representasi);
    formData.append("nama_hotel", cost.nama_hotel);
    formData.append("biaya_kontribusi", cost.biaya_kontribusi);
    formData.append("biaya_akomodasi", cost.biaya_akomodasi);
    formData.append("biaya_tiket_berangkat", cost.biaya_tiket_berangkat);
    formData.append("biaya_tiket_kembali", cost.biaya_tiket_kembali);
    formData.append("biaya_bantuan_transport", cost.biaya_bantuan_transport);
    formData.append("biaya_taxi_berangkat", cost.biaya_taxi_berangkat);
    formData.append("biaya_taxi_kembali", cost.biaya_taxi_kembali);
    formData.append("biaya_travel", cost.biaya_travel);
    formData.append("biaya_tidak_menginap", cost.biaya_tidak_menginap);
    formData.append("lama_hari", lamaHari.value);
    formData.append("total_uang_harian", totalPerhari.value);
    formData.append("total_biaya", totalBiaya.value);

    if (buktiFile.value) {
      formData.append("bukti_file", buktiFile.value);
    }

    const res = await axios.post(`/api/costs/${route.params.id}?_method=PUT`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    alert(res.data.message || "Data berhasil diupdate");
    router.push("/admin/rincian-biaya");
  } catch (error) {
    console.error(error.response?.data || error.message);
    alert("Gagal update rincian biaya");
  }
};

onMounted(() => {
  fetchCost();
});
</script>
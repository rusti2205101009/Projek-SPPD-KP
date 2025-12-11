<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto mb-4">
      <router-link
        to="/admin/kepala"
        class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
      >
        Kembali
      </router-link>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Edit Data Kepala Bagian</h1>
      </div>

      <form v-if="formLoaded" @submit.prevent="handleSubmit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-5">
        <div>
          <label class="block mb-1 font-semibold text-gray-700">Foto</label>
          <input
            type="file"
            @change="(e) => handleFileChange(e, 'foto')"
            accept="image/*"
            class="w-full border border-gray-300 rounded px-3 py-2 bg-white 
              file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 
              file:text-sm file:font-semibold file:bg-blue-600 file:text-white 
              hover:file:bg-blue-700"
          />
          <div v-if="previewFoto" class="mt-3">
            <img
              :src="previewFoto"
              alt="Preview"
              class="w-32 h-32 object-cover rounded border border-gray-300"
            />
          </div>
          <div v-else class="mt-2 text-gray-400">Tidak ada foto</div>
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">NIP</label>
          <input
            v-model="head.nip"
            type="text"
            maxlength="18"
            required
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Gelar Depan</label>
          <input
            v-model="head.gelar_depan"
            type="text"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Nama</label>
          <input
            v-model="head.nama_kepala"
            type="text"
            required
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Gelar Belakang</label>
          <input
            v-model="head.gelar_belakang"
            type="text"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>
        </div>

        <div class="space-y-5">
        <div>
          <label class="block mb-1 font-semibold text-gray-700">Jabatan</label>
          <input
            v-model="head.jabatan"
            type="text"
            required
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Pangkat</label>
          <input
            v-model="head.pangkat"
            type="text"
            required
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Golongan</label>
          <input
            v-model="head.golongan"
            type="text"
            required
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Tanda Tangan</label>
            <input
              type="file"
              @change="(e) => handleFileChange(e, 'ttd')"
              accept="image/*"
              class="w-full border border-gray-300 rounded px-3 py-2 bg-white 
                file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 
                file:text-sm file:font-semibold file:bg-blue-600 file:text-white 
                hover:file:bg-blue-700"
            />
            <div v-if="previewTTD" class="mt-3">
              <img
                :src="previewTTD"
                alt="Preview TTD"
                class="w-32 h-20 object-contain border border-gray-300 bg-gray-50"
              />
            </div>
          <div v-else class="mt-2 text-gray-400">Tidak ada TTD</div>
        </div>
        </div>

        <div class="pt-4 col-span-full flex justify-end">
          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold"
          >
            Update
          </button>
        </div>
      </form>

      <div v-else>
        <p>Loading data...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";

const router = useRouter();
const route = useRoute();

const head = reactive({
  id: null,
  foto: null,
  nip: "",
  gelar_depan: "",
  nama_kepala: "",
  gelar_belakang: "",
  jabatan: "",
  pangkat: "",
  golongan: "",
  ttd: null,
});

const previewFoto = ref(null);
const previewTTD = ref(null);
const formLoaded = ref(false);

const handleFileChange = (e, type) => {
  const file = e.target.files[0];
  if (!file) return;

  head[type] = file;

  if (type === "foto") previewFoto.value = URL.createObjectURL(file);
  if (type === "ttd") previewTTD.value = URL.createObjectURL(file);
};

const loadKepala = async () => {
  try {
    const response = await axios.get(
      `http://localhost:8000/api/head_divisions/${route.params.id}`
    );
    const data = response.data.data;
    head.nip = data.nip;
    head.gelar_depan = data.gelar_depan;
    head.nama_kepala = data.nama_kepala;
    head.gelar_belakang = data.gelar_belakang;
    head.jabatan = data.jabatan;
    head.pangkat = data.pangkat;
    head.golongan = data.golongan;

    previewFoto.value = data.foto || null;
    previewTTD.value = data.ttd || null;

  } catch (error) {
    alert("Gagal memuat data: " + error);
  } finally {
    formLoaded.value = true;
  }
};

const handleSubmit = async () => {
  try {
    const data = new FormData();
    if (head.foto instanceof File) data.append("foto", head.foto);
    if (head.ttd instanceof File) data.append("ttd", head.ttd);

    data.append("nip", head.nip);
    data.append("gelar_depan", head.gelar_depan);
    data.append("nama_kepala", head.nama_kepala);
    data.append("gelar_belakang", head.gelar_belakang);
    data.append("jabatan", head.jabatan);
    data.append("pangkat", head.pangkat);
    data.append("golongan", head.golongan);

    await axios.post(`/api/head_divisions/${route.params.id}?_method=PUT`, data, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    alert("Data berhasil diperbarui");
    router.push("/admin/kepala");
  } catch (error) {
    alert("Gagal update data: " + (error.response?.data?.message || error.message));
  }
};

onMounted(() => {
  loadKepala();
});
</script>
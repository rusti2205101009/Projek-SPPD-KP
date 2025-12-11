<template>
  <div class="min-h-screen bg-gray-100 p-6">
    
    <div class="max-w-3xl mx-auto mb-4">
      <router-link
        to="/admin/pegawai"
        class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
      >
        Kembali
      </router-link>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Edit Data Pegawai</h1>
      </div>

      <form v-if="formLoaded" @submit.prevent="handleEmployeeSubmit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-5">
        <div>
          <label class="block mb-1 font-semibold text-gray-700">NIP/NIPPPK</label>
          <input
            v-model="form.nip_nipppk"
            type="text"
            maxlength="18"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Gelar Depan</label>
          <input
            v-model="form.gelar_depan"
            type="text"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Nama Pegawai</label>
          <input
            v-model="form.nama_pegawai"
            type="text"
            required
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>
        <div>
          <label class="block mb-1 font-semibold text-gray-700">Gelar Belakang</label>
          <input
            v-model="form.gelar_belakang"
            type="text"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>
        </div>

        <div class="space-y-5">
        <div>
          <label class="block mb-1 font-semibold text-gray-700">Jabatan</label>
          <input
            v-model="form.jabatan"
            type="text"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Pangkat</label>
          <input
            v-model="form.pangkat"
            type="text"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Golongan</label>
          <input
            v-model="form.golongan"
            type="text"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Foto</label>
          <input
            type="file"
            @change="handleFileChange"
            accept="image/*"
            class="w-full border border-gray-300 rounded px-3 py-2 bg-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
          />
          <div v-if="preview" class="mt-3">
            <img
              :src="preview"
              alt="Preview"
              class="w-32 h-32 object-cover rounded border border-gray-300"
            />
          </div>
          <div v-else class="mt-2 text-gray-400">Tidak ada foto</div>
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

const route = useRoute();
const router = useRouter();

const form = reactive({
  foto: null, 
  nip_nipppk: "",
  gelar_depan: "",
  nama_pegawai: "",
  gelar_belakang: "",
  jabatan: "",
  pangkat: "",
  golongan: "",
});

const preview = ref(null); 
const formLoaded = ref(false);

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.foto = file; 
    preview.value = URL.createObjectURL(file);
  } else {
    form.foto = null;
    preview.value = null;
  }
};

const loadEmployee = async () => {
  try {
    const response = await axios.get(
      `http://localhost:8000/api/employees/${route.params.id}`
    );
    const data = response.data.data;
    form.nip_nipppk = data.nip_nipppk;
    form.gelar_depan = data.gelar_depan || '';
    form.nama_pegawai = data.nama_pegawai;
    form.gelar_belakang = data.gelar_belakang || '';
    form.jabatan = data.jabatan || '';
    form.pangkat = data.pangkat || '';
    form.golongan = data.golongan || '';

    form.foto = null; 
    preview.value = data.foto; 
  } catch (error) {
    alert("Gagal memuat data: " + error);
  } finally {
    formLoaded.value = true;
  }
};

const handleEmployeeSubmit = async () => {
  try {
    const data = new FormData();
    if (form.foto && form.foto instanceof File) {
      data.append("foto", form.foto);
    }
    data.append("nip_nipppk", form.nip_nipppk);
    data.append("gelar_depan", form.gelar_depan);
    data.append("nama_pegawai", form.nama_pegawai);
    data.append("gelar_belakang", form.gelar_belakang);
    data.append("jabatan", form.jabatan);
    data.append("pangkat", form.pangkat);
    data.append("golongan", form.golongan);

    await axios.post(
      `http://localhost:8000/api/employees/${route.params.id}?_method=PUT`,
      data,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      }
    );

    alert("Data berhasil diperbarui");
    router.push("/admin/pegawai");
  } catch (error) {
    alert(
      "Gagal memperbarui data: " + (error.response?.data?.message || error.message)
    );
  }
};

onMounted(() => {
  loadEmployee();
});

</script>
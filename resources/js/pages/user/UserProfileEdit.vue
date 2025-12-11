<template>
  <main class="flex-1 p-6 overflow-y-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Profil User</h1>
    </div>

    <router-link
      to="/user/dashboard"
      class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
    >
      Kembali
    </router-link>

    <div class="bg-white rounded shadow max-w-4xl p-6">
      <form @submit.prevent="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block mb-1 font-semibold">Nama</label>
            <input
              type="text"
              :value="profile.nama|| ''"
              readonly
              class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed"
            />
          </div>

          <div>
            <label class="block mb-1 font-semibold">NIP/NIPPPK</label>
            <input
              type="text"
              :value="profile.nip_nipppk || ''"
              readonly
              class="w-full border rounded px-3 py-2 bg-gray-100 cursor-not-allowed"
            />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Jenis Kelamin</label>
            <select
              v-model="form.jenis_kelamin"
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            >
              <option value="">Pilih jenis kelamin</option>
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
          </div>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block mb-1 font-semibold">No. Handphone</label>
            <input
              type="text"
              v-model="form.nohp"
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Email</label>
            <input
              type="text"
              v-model="form.email"
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            />
          </div>

          <div>
            <label class="block mb-1 font-semibold">Foto</label>
            <input
              type="file"
              @change="handleFile"
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-green-500"
            />
            <div v-if="preview" class="mt-2">
              <img :src="preview" alt="Preview Foto Baru" class="w-24 h-24 rounded object-cover border" />
            </div>
            <div v-if="profile.foto" class="mt-2">
              <img :src="profile.foto" alt="Foto Profil" class="w-24 h-24 rounded object-cover border" />
            </div>
          </div>

          <div class="pt-4 col-span-full flex justify-end">
            <button
              type="submit"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
            >
              Simpan
            </button>
          </div>
        </div>
      </form>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const profile = ref({});
const form = ref({
  jenis_kelamin: "",
  email: "",
  nohp: "",
  foto: null,
});

const preview = ref(null);

const getProfile = async () => {
  try {
    const { data } = await axios.get("/api/profile");
    profile.value = data.data;

    form.value.jenis_kelamin = profile.value.jenis_kelamin || "";
    form.value.email = profile.value.email || "";
    form.value.nohp = profile.value.nohp || "";
  } catch (error) {
    console.error(error);
  }
};

const handleFile = (e) => {
  const file = e.target.files[0];
  form.value.foto = file;
  preview.value = file ? URL.createObjectURL(file) : null;
};

const updateProfile = async () => {
  try {
    const formData = new FormData();
    formData.append("jenis_kelamin", form.value.jenis_kelamin);
    formData.append("email", form.value.email);
    formData.append("nohp", form.value.nohp);
    if (form.value.foto) {
      formData.append("foto", form.value.foto);
    }

    // debug
    //  for (let pair of formData.entries()) {
    //   console.log(pair[0] + ": " + pair[1]);
    // }

    const { data } = await axios.post("/api/profile", formData);

    profile.value = data.data;
    alert("Profil berhasil diperbarui!");
    router.push('/admin/dashboard');
  } catch (error) {
    console.error(error);
    alert("Terjadi kesalahan saat update profil");
  }
};

onMounted(() => {
  getProfile();
});
</script>
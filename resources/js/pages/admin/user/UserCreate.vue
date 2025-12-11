<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto mb-4">
      <router-link
        to="/admin/user"
        class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
      >
        Kembali
      </router-link>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Tambah User</h1>
      </div>

      <form @submit.prevent="handleSubmitUser" class="space-y-5">
        <div>
          <label class="block mb-1 font-semibold text-gray-700">Pegawai</label>
          <select
            v-model="pegawai.employee_id"
            @change="fillPegawai"
            required
            class="w-full border border-gray-300 rounded px-3 py-2"
          >
            <option value="">-- Pilih Pegawai --</option>
            <option
              v-for="pegawai in employees"
              :key="pegawai.id"
              :value="pegawai.id"
            >
              {{ pegawai.nama_pegawai }}
            </option>
          </select>
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">NIP/NIPPPK</label>
          <input
            v-model="pegawai.nip_nipppk"
            type="text"
            readonly
            class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Password</label>
          <input
            v-model="pegawai.password"
            type="password"
            required
            minlength="6"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Role</label>
          <select
            v-model="pegawai.role"
            required
            class="w-full border border-gray-300 rounded px-3 py-2"
          >
            <option value="">-- Pilih Role --</option>
            <option value="admin">admin</option>
            <option value="user">user</option>
          </select>
        </div>

        <div>
          <label class="block mb-1 font-semibold text-gray-700">Foto</label>
          <input
            type="file"
            accept="image/*"
            @change="handleFotoChange"
            class="w-full border border-gray-300 rounded px-3 py-2"
          />
          <div v-if="fotoPreview" class="mt-2">
            <img :src="fotoPreview" alt="Preview Foto" class="w-24 h-24 rounded-full object-cover border" />
          </div>
        </div>

        <div class="pt-4 flex justify-end">
          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold"
          >
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const employees = ref([]);

const pegawai = reactive({
  employee_id: "",
  nip_nipppk: "",
  password: "",
  role: "",
  foto: null,
});

const fotoPreview = ref(null);

const loadEmployees = async () => {
  try {
    const res = await axios.get("/api/employees/available", {
      params: { per_page: 100 },
    });
    employees.value = res.data?.data || [];
  } catch (e) {
    alert("Gagal memuat pegawai: " + e.message);
  }
};

const fillPegawai = () => {
  const selected = employees.value.find((p) => p.id === pegawai.employee_id);
  pegawai.nip_nipppk = selected ? selected.nip_nipppk : "";
};

const handleFotoChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    pegawai.foto = file;
    fotoPreview.value = URL.createObjectURL(file);
  } else {
    pegawai.foto = null;
    fotoPreview.value = null;
  }
};

// const handleSubmitUser = async () => {
//   try {
//     await axios.post("/api/users", pegawai);
//     alert("User berhasil dibuat");
//     router.push("/admin/user");
//   } catch (e) {
//     console.error("DETAIL ERROR:", e.response?.data);
//     alert("Gagal menyimpan user: " + (e.response?.data?.message || e.message));
//   }
// };

const handleSubmitUser = async () => {
  try {
    const formData = new FormData();
    formData.append("employee_id", pegawai.employee_id);
    formData.append("nip_nipppk", pegawai.nip_nipppk);
    formData.append("password", pegawai.password);
    formData.append("role", pegawai.role);
    if (pegawai.foto) {
      formData.append("foto", pegawai.foto);
    }

    await axios.post("/api/users", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    alert("User berhasil dibuat");
    router.push("/admin/user");
  } catch (e) {
    console.error("DETAIL ERROR:", e.response?.data);
    alert("Gagal menyimpan user: " + (e.response?.data?.message || e.message));
  }
};

onMounted(loadEmployees);
</script>
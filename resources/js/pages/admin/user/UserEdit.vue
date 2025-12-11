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
        <h1 class="text-2xl font-bold text-blue-700">Edit User</h1>
      </div>
    <form @submit.prevent="updateUser" class="space-y-4 mt-7">
      <div>
        <label class="block text-sm font-medium">Nama Pegawai</label>
        <select 
          v-model="pegawai.employee_id" 
          @change="fillPegawai"
          class="w-full border rounded px-3 py-2">
          <option value="">-- Pilih Pegawai --</option>
          <option v-for="emp in employees" :key="emp.id" :value="emp.id">
            {{ emp.nama_pegawai }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">NIP/NIPPPK</label>
        <input
          v-model="pegawai.nip_nipppk"
          type="text"
          class="w-full border rounded px-3 py-2"
          maxlength="18"
          readonly
        />
      </div>

      <div>
        <label class="block text-sm font-medium">Password (opsional)</label>
        <input
          v-model="pegawai.password"
          type="password"
          class="w-full border rounded px-3 py-2"
        />
        <small class="text-gray-500">Kosongkan jika tidak ingin ganti password</small>
      </div>

      <div>
        <label class="block text-sm font-medium">Role</label>
        <select v-model="pegawai.role" class="w-full border rounded px-3 py-2">
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">Foto</label>
          <input
            type="file"
            accept="image/*"
            @change="handleFotoChange"
            class="w-full border rounded px-3 py-2"
          />
          <div v-if="fotoPreview || fotoLama.foto" class="mt-2">
            <p class="text-xs text-gray-500 mb-1">Preview:</p>
            <img
              :src="fotoPreview || fotoLama.foto"
              alt="Preview Foto"
              class="w-24 h-24 rounded-full object-cover border"
          />
        </div>
      </div>

      <div class="pt-4 flex justify-end">
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Update
        </button>
      </div>
    </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"
import axios from "axios"

const route = useRoute()
const router = useRouter()
const id = route.params.id

const pegawai = ref({
  employee_id: "",
  nip_nipppk: "",
  password: "",
  role: "user",
  foto: null,
})

const fotoLama = ref({
  foto: null 
})

const fotoPreview = ref(null)

const employees = ref([])

const fetchUser = async () => {
  try {
    const res = await axios.get(`/api/users/${id}`)
    const user = res.data.data
    pegawai.value = {
      employee_id: user.employee_id ?? "",
      nip_nipppk: user.nip_nipppk,
      password: "",
      role: user.role,
      foto: null
    }
    fotoLama.value.foto = user.foto || null
    await fetchEmployees(user.employee_id)
  } catch (e) {
    console.error("DETAIL ERROR:", e.response?.data);
    alert("Gagal mengambil data user")
  }
}

const fetchEmployees = async (currentEmployeeId = null) => {
  try {
    const res = await axios.get("/api/employees/available")
    let list = res.data.data ?? res.data
    if (currentEmployeeId && !list.find(p => p.id === currentEmployeeId)) {
      const detail = await axios.get(`/api/employees/${currentEmployeeId}`)
      list.push(detail.data.data)
    }
    employees.value = list
  } catch (e) {
    employees.value = []
  }
}

const fillPegawai = () => {
  const selected = employees.value.find(p => p.id == pegawai.value.employee_id)
  pegawai.value.nip_nipppk = selected ? selected.nip_nipppk : ''
}

const handleFotoChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    pegawai.value.foto = file
    fotoPreview.value = URL.createObjectURL(file)
  } else {
    pegawai.value.foto = null
    fotoPreview.value = null
  }
}

const updateUser = async () => {
  try {
    const formData = new FormData()
    formData.append("employee_id", pegawai.value.employee_id)
    formData.append("nip_nipppk", pegawai.value.nip_nipppk)
    formData.append("role", pegawai.value.role)
    if (pegawai.value.password) formData.append("password", pegawai.value.password)
    if (pegawai.value.foto) formData.append("foto", pegawai.value.foto)

    await axios.post(`/api/users/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" }
    })

    alert("User berhasil diperbarui")
    router.push("/admin/user")
  } catch (e) {
    alert("Gagal update user: " + (e.response?.data?.message || e.message))
  }
}

onMounted(() => {
  fetchUser()
})
</script>
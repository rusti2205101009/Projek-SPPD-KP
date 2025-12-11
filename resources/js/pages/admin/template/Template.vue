<template>
  <div class="p-6 bg-white rounded shadow max-w-md mx-auto">
    <h2 class="text-xl font-bold mb-4">Upload Template SPT</h2>

    <div class="mb-6 border rounded p-4 bg-gray-50">
      <h3 class="font-semibold mb-2">Data Template Saat Ini</h3>

      <table class="w-full text-sm border border-gray-300">
        <thead>
          <tr class="bg-gray-200 text-left">
            <th class="p-2 border">No</th>
            <th class="p-2 border">Nama Template</th>
            <th class="p-2 border">Status</th>
            <th class="p-2 border">File</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="templates.length" v-for="(t, i) in templates" :key="t.id">
            <td class="p-2 border text-center">{{ i + 1 }}</td>
            <td class="p-2 border">{{ t.spt }}</td>
            <td class="p-2 border" :class="i === 0 ? 'text-green-600 font-semibold' : 'text-gray-500'">
              {{ i === 0 ? 'Aktif (Terbaru)' : 'Lama' }}
            </td>
            <td class="p-2 border">
              <a :href="t.file_url" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                Lihat File
              </a>
            </td>
          </tr>
          <tr v-else>
            <td colspan="4" class="text-center p-2 border text-gray-500">Belum ada template</td>
          </tr>
        </tbody>
      </table>
    </div>

    <form @submit.prevent="uploadTemplate">
      <div class="mb-4">
        <label class="block mb-1 font-semibold">Nama SPT</label>
        <input v-model="spt" type="text" class="w-full border rounded px-3 py-2" required>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-semibold">File</label>
        <input type="file" @change="handleFile" accept=".doc,.docx" required class="w-full border border-gray-300 rounded px-3 py-2 cursor-pointer">
      </div>

      <div class="flex justify-center">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Upload
      </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const spt = ref('')
const file = ref(null)
const templates = ref([])

const handleFile = (e) => {
  file.value = e.target.files[0]
}

const fetchTemplate = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get('/api/templates', {
      headers: { Authorization: `Bearer ${token}` }
    })
    templates.value = res.data.data.map(t => ({
      ...t,
      file_url: `/storage/${t.file_path}`
    }))
  } catch (err) {
    console.error(err)
    template.value = null
  }
}

const uploadTemplate = async () => {
  if (!file.value) return alert('Pilih file terlebih dahulu.')

  const formData = new FormData()
  formData.append('spt', spt.value)
  formData.append('file', file.value)

  try {
    const token = localStorage.getItem('token')
    const res = await axios.post('/api/templates', formData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    })
    alert(res.data.message)
    spt.value = ''
    file.value = null
    // router.push('/admin/dashboard') 
    await fetchTemplate()
  } catch (err) {
    console.error(err)
    alert('Gagal upload template')
  }
}

onMounted(fetchTemplate)
</script>
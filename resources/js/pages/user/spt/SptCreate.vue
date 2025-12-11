<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Tambah Surat Perintah Tugas</h1>

    <router-link
      to="/user/spt"
      class="text-sm bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded font-semibold mb-4 inline-block"
    >
      Kembali
    </router-link>

    <form @submit.prevent="submitFormSpt" class="bg-white p-6 rounded-lg shadow-md grid grid-cols-1 md:grid-cols-2 gap-4">

      <div>
        <label class="block mb-1 font-semibold">Nama Pegawai</label>
        <Multiselect
          v-model="selectedPegawai"
          :options="daftarPegawai"
          :multiple="true"
          track-by="id"
          placeholder="Ketik nama pegawai"
          :custom-label="pegawaiLabel"
          class="w-full"
          :searchable="true" 
          :filterable="true" 
          :allow-empty="true"
          @search-change="searchPegawai"
        />
      </div>

      <template v-for="(pegawai, index) in spts.pegawai_list" :key="pegawai.employee_id">
        <div class="col-span-1 md:col-span-2 border p-4 bg-gray-50 rounded mb-4">
          <h2 class="font-semibold text-green-700 mb-2">Pegawai {{ index + 1 }}</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block mb-1 font-semibold">Nama Pegawai</label>
              <input type="text" :value="gabungNama(pegawai)" class="w-full border rounded px-3 py-2 bg-gray-200" readonly>
            </div>
            <div>
              <label class="block mb-1 font-semibold">NIP/NIPPPK</label>
              <input type="text" :value="pegawai.nip_nipppk" class="w-full border rounded px-3 py-2 bg-gray-200" readonly>
            </div>
            <div>
              <label class="block mb-1 font-semibold">Jabatan</label>
              <input type="text" :value="pegawai.jabatan" class="w-full border rounded px-3 py-2 bg-gray-200" readonly>
            </div>
            <div>
              <label class="block mb-1 font-semibold">Pangkat</label>
              <input type="text" :value="pegawai.pangkat" class="w-full border rounded px-3 py-2 bg-gray-200" readonly>
            </div>
            <div>
              <label class="block mb-1 font-semibold">Golongan</label>
              <input type="text" :value="pegawai.golongan" class="w-full border rounded px-3 py-2 bg-gray-200" readonly>
            </div>
            <div>
              <label class="block mb-1 font-semibold">Bidang/Unit</label>
              <!-- <input type="text" v-model="pegawai.bidang" class="w-full border rounded px-3 py-2 bg-white" placeholder="Isi bidang" required> -->
              <input type="text" v-model="pegawai.bidang" class="w-full border rounded px-3 py-2 bg-white" placeholder="Isi bidang">
            </div>
          </div>
        </div>
      </template>

      <div>
        <label class="block mb-1 font-semibold">Nomor Surat</label>
        <input v-model="spts.nomor_surat" type="text" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold">Tanggal Surat</label>
        <!-- <input v-model="spts.tanggal_surat" type="date" :min="today" class="w-full border rounded px-3 py-2" required> -->
        <input v-model="spts.tanggal_surat" type="date" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold">Keperluan Perjalanan Dinas</label>
        <input v-model="spts.keperluan" type="text" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold">Nama Kepala Bagian</label>
        <Multiselect
          v-model="selectedKepala"
          :options="daftarKepala"
          :multiple="false"
          track-by="id"
          placeholder="Pilih Kepala Divisi"
          :custom-label="kepalaLabel"
          class="w-full border rounded"
        />
      </div>

      <div class="col-span-1 md:col-span-2 text-right">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-semibold">Simpan</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import Multiselect from 'vue-multiselect'

const router = useRouter()

const daftarPegawai = ref([])
const selectedPegawai = ref([])

const daftarKepala = ref([])
const selectedKepala = ref(null)

const today = new Date().toISOString().split("T")[0];

const spts = ref({
  pegawai_list: [],
  nomor_surat: '',
  tanggal_surat: '',
  keperluan: '',
  head_division_id: ''
})

const pegawaiLabel = (p) => {
  if (!p) return ''
  const depan = p.gelar_depan ? p.gelar_depan + ' ' : ''
  const belakang = p.gelar_belakang ? ', ' + p.gelar_belakang : ''
  return `${depan}${p.nama_pegawai}${belakang}`
}

const kepalaLabel = (k) => {
  if (!k) return ''
  const depan = k.gelar_depan ? k.gelar_depan + ' ' : ''
  const belakang = k.gelar_belakang ? ', ' + k.gelar_belakang : ''
  return `${depan}${k.nama_kepala}${belakang}`
}

const gabungNama = (p) => {
  const depan = p.gelar_depan ? p.gelar_depan + ' ' : ''
  const belakang = p.gelar_belakang ? ', ' + p.gelar_belakang : ''
  return `${depan}${p.nama_pegawai}${belakang}`
}

const fetchPegawai = async (query = '') => {
  try {
    const res = await axios.get('/api/employees/dropdown', {
      params: {
        // per_page: 10, 
        search: query 
      }
    })
    daftarPegawai.value = res.data
    // daftarPegawai.value = res.data.data
  } catch (err) {
    console.error('Gagal memuat pegawai:', err)
  }
}

const searchPegawai = (query) => {
  fetchPegawai(query)
}

const fetchKepala = async () => {
  try {
    const res = await axios.get('/api/head_divisions')
    daftarKepala.value = res.data.data
  } catch (err) {
    console.error('Gagal memuat kepala:', err)
  }
}

const isiDataPegawai = () => {
  spts.value.pegawai_list = selectedPegawai.value.map(p => ({
    employee_id: p.id,
    nama_pegawai: p.nama_pegawai,
    gelar_depan: p.gelar_depan,
    gelar_belakang: p.gelar_belakang,
    nip_nipppk: p.nip_nipppk,
    jabatan: p.jabatan,
    pangkat: p.pangkat,
    golongan: p.golongan,
    bidang: ''
  }))
}

watch(selectedPegawai, isiDataPegawai)
watch(selectedKepala, (val) => {
  spts.value.head_division_id = val ? val.id : null
})

const submitFormSpt = async () => {
  try {
    const payload = {
      ...spts.value,
      pegawai_list: spts.value.pegawai_list.map(p => ({
        employee_id: p.employee_id,
        bidang: p.bidang
      }))
    }
    console.log('Payload:', payload) // debug
    await axios.post('/api/spts', payload)
    alert('STP berhasil disimpan!')
    router.push("/user/spt")
  } catch (err) {
    console.error("DETAIL ERROR:", err.response?.data);  // penjelas error
    alert('Gagal menyimpan STP')
  }
}

onMounted(() => {
  fetchPegawai()
  fetchKepala()
})
</script>
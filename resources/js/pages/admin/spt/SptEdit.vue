<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Edit Surat Perintah Tugas</h1>

    <router-link
      to="/admin/spt"
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
          placeholder="Pilih Pegawai"
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
            <input type="hidden" :value="pegawai.employee_id" />
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
              <input type="text" v-model="pegawai.bidang" class="w-full border rounded px-3 py-2 bg-white" placeholder="Isi bidang">
            </div>
          </div>
        </div>
      </template>

      <!-- Form STP -->
      <div>
        <label class="block mb-1 font-semibold">Nomor Surat</label>
        <input v-model="spts.nomor_surat" type="text" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold">Tanggal Surat</label>
        <input v-model="spts.tanggal_surat" type="date" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold">Keperluan Perjalanan Dinas</label>
        <input v-model="spts.keperluan" type="text" :min="today" class="w-full border rounded px-3 py-2" required>
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
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">Simpan</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import Multiselect from 'vue-multiselect'

const router = useRouter()
const route = useRoute()
const id = route.params.id

const daftarPegawai = ref([])
const daftarKepala = ref([])

const selectedPegawai = ref([])
const selectedKepala = ref(null)

const today = new Date().toISOString().split("T")[0];

const spts = ref({
  pegawai_list: [],
  nomor_surat: '',
  tanggal_surat: '',
  keperluan: '',
  head_division_id: ''
})

const fetchPegawai = async (query = '') => {
  try {
    const res = await axios.get('/api/employees/dropdown', {
      params: {
        // per_page: 10,
        search: query
      }
    })
    daftarPegawai.value = res.data
    // console.log("Fetch Pegawai", daftarPegawai.value.map(p => p.id))
  } catch (err) {
    console.error('Gagal memuat pegawai:', err)
  }
}

const fetchKepala = async () => {
  const res = await axios.get('/api/head_divisions')
  daftarKepala.value = res.data.data
  // console.log("Fetch Kepala", daftarKepala.value.map(k => k.id))
}

const isInitialLoad = ref(true)

watch(selectedPegawai, () => {
  // console.log("Watch selectedPegawai triggered")
  if (!isInitialLoad.value) {
    isiDataPegawai()
    // logState("Setelah isiDataPegawai oleh watch")
  }
})

const fetchStp = async () => {
  // console.log("Fetch SPT:", id)
  const res = await axios.get(`/api/spts/${id}`)
  const data = res.data.data

  spts.value.nomor_surat = data.nomor_surat
  spts.value.tanggal_surat = data.tanggal_surat
  spts.value.keperluan = data.keperluan
  spts.value.head_division_id = data.head_division?.id || null

  selectedKepala.value = data.head_division || null

  const bidangMap = {}
  data.employees.forEach(p => {
    bidangMap[p.id] = p.bidang || ''
  })

  spts.value.pegawai_list = data.employees.map(p => ({
    employee_id: p.employee_id,
    nama_pegawai: p.nama_pegawai,
    nip_nipppk: p.nip_nipppk,
    jabatan: p.jabatan,
    pangkat: p.pangkat,
    golongan: p.golongan,
    bidang: bidangMap[p.id]
  }))

  selectedPegawai.value = data.employees.map(e => ({
    id: e.employee_id,
    nama_pegawai: e.nama_pegawai,
    gelar_depan: e.gelar_depan,
    gelar_belakang: e.gelar_belakang,
    nip_nipppk: e.nip_nipppk,
    jabatan: e.jabatan,
    pangkat: e.pangkat,
    golongan: e.golongan,
    bidang: e.bidang || ''
  }))
   isInitialLoad.value = false
}

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

const isiDataPegawai = () => {
  spts.value.pegawai_list = selectedPegawai.value.map(p => {
    const existing = spts.value.pegawai_list.find(pl => pl.employee_id === p.id) || {}
    return {
      employee_id: p.id,
      nama_pegawai: p.nama_pegawai,
      gelar_depan: p.gelar_depan,
      gelar_belakang: p.gelar_belakang,
      nip_nipppk: p.nip_nipppk,
      jabatan: p.jabatan,
      pangkat: p.pangkat,
      golongan: p.golongan,
      bidang: existing.bidang || ''
    }
  })
}

watch(selectedKepala, val => {
  spts.value.head_division_id = val ? val.id : null
  // console.log("Kepala Divisi Dipilih:", spts.value.head_division_id)
})

const submitFormSpt = async () => {
  const finalPegawai = selectedPegawai.value.length > 0
    ? spts.value.pegawai_list
    : spts.value.pegawai_list;

  const payload = {
    nomor_surat: spts.value.nomor_surat,
    tanggal_surat: spts.value.tanggal_surat,
    keperluan: spts.value.keperluan,
    head_division_id: spts.value.head_division_id,
    pegawai_list: finalPegawai.map(p => ({
      employee_id: p.employee_id,
      bidang: p.bidang || ''
    }))
  }

  // console.log("Final Payload:", JSON.parse(JSON.stringify(payload)))

  // console.log("Cek Pegawai List Detail:")
  // payload.pegawai_list.forEach((p, i) => {
  //   console.log(`Index ${i}:`, {
  //     employee_id: p.employee_id,
  //     bidang: p.bidang,
  //     raw: p
  //   });
  // });

  try {
    await axios.put(`/api/spts/${id}`, payload)
    alert('Surat Perintah Tugas berhasil diperbarui')
    router.push("/admin/spt")
  } catch (err) {
    console.error("DETAIL ERROR:", err.response?.data);
    alert('Gagal memperbarui Surat Perintah Tugas')
  }
}

const searchPegawai = async (query) => {
  await fetchPegawai(query)
}

onMounted(async () => {
  await fetchPegawai()
  await fetchKepala()
  await fetchStp()

  selectedPegawai.value.forEach(p => {
    if (!daftarPegawai.value.find(dp => dp.id === p.id)) {
      daftarPegawai.value.push(p)
    }
  })

  isiDataPegawai()
})
</script>
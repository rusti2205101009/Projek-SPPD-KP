<template>
  <div>
    <h2 class="text-2xl font-semibold">Dashboard User</h2>
    <p class="mt-4 mb-6">Selamat datang di aplikasi SPPD.</p>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      <router-link
        v-for="(item, key) in stats"
        :key="key"
        :to="item.route"
        class="bg-white rounded-lg shadow p-2 flex flex-col items-center justify-center hover:shadow-md transition cursor-pointer"
      >
        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-100 text-green-600 text-2xl">
          <i :class="item.icon"></i>
        </div>
        <div class="mt-3 text-2xl font-bold text-gray-700">
          {{ item.count }}
        </div>
        <span class="font-medium mt-1 capitalize text-gray-600">{{ item.label }}</span>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const stats = ref({
  pegawai: { label: "Pegawai", count: 0, icon: "fas fa-user-tie", route: "/user/pegawai" },
  spt: { label: "SPT", count: 0, icon: "fas fa-file-signature", route: "/user/spt" },
  sppd: { label: "SPPD", count: 0, icon: "fas fa-file-alt", route: "/user/sppd" },
});

onMounted(async () => {
  try {
    const token = localStorage.getItem("token");
    const res = await axios.get("/api/dashboard-stats", {
      headers: { Authorization: `Bearer ${token}` },
    });

    stats.value.pegawai.count = res.data.pegawai;
    stats.value.spt.count = res.data.spt;
    stats.value.sppd.count = res.data.sppd;
  } catch (e) {
    console.error("Gagal ambil stats:", e);
  }
});
</script>
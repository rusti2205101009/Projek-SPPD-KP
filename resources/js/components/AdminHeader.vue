<template>
  <header class="bg-linear-to-r from-blue-700 to-cyan-500 shadow p-4 flex items-center relative">
    <img
      :src="logo"
      alt="Logo"
      class="h-12 w-auto object-contain absolute left-4"
    />
    <h1 class="text-xl font-bold text-stone-100 mx-auto text-center max-w-xs sm:max-w-none">
      <span>Bagian Hukum Sekretariat Daerah </span>
      <br class="block sm:hidden" />
      <span class="block sm:inline"> Kabupaten Madiun</span>
    </h1>
  </header>
</template>

<script setup>
import logo from '../assets/Logo_kabupaten_madiun.png'
</script>
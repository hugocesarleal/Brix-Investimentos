<template>
    <v-btn 
    variant="outlined"
    :block="block"
    :text="text"
    :loading="loading"
  />
  </template>
  <script setup>
  const props = defineProps(['text', 'loading', 'block'])
  </script>
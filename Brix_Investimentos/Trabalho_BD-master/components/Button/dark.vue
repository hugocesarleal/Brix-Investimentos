<template>
    <v-btn 
    type="submit"
    color="primary-darken-1"
    :block="block"
    class="mt-2"
    :text="text"
    :loading="loading"
  />
</template>
<script setup>
  const props = defineProps(['text', 'loading', 'block'])
</script>
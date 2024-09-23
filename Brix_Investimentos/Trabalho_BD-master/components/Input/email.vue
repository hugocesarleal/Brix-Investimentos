<template>
    <v-text-field 
      variant="outlined"
      label="E-mail"
      :disabled="false"
      v-model="value"
    />
  </template>
  <script setup>
    import { computed } from 'vue'
  
    const props = defineProps(['modelValue'])
    const emit = defineEmits(['update:modelValue'])
  
    const value = computed({
      get() {
        return props.modelValue
      },
      set(value) {
        emit('update:modelValue', value)
      }
    })
  </script>
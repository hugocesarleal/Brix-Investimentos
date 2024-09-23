<template>
  <v-app class="bg-gradient-purple">
    <NuxtLayout :name="getLayout()">
      <v-container class="main-content">
        <NuxtPage />
      </v-container>
    </NuxtLayout>
  </v-app>
</template>

<script setup>
let route = useRoute()

function getLayout() {
  if (route.path === '/login') {
    return 'login'
  }
  if (route.path === '/relatorio') {
    return 'relatorio'
  }
  if (route.path === '/forgotPassword') {
    return 'forgotPassword'
  }
  if (route.path === '/cadastro') {
    return 'cadastro'
  }
  if (route.path === '/historico') {
    return 'historico'
  }
  if (route.path === '/compra_venda') {
    return 'compra_venda'
  }
}


</script>

<style>
.main-content {
  margin-top: 50px;
}

.bg-gradient-purple {
  background: #4A3AFF !important;
  background: linear-gradient(99deg, #abc4ff 0%, #9f9ad8 1%, #FFFFFF 100%) !important; 
  background-repeat: no-repeat !important;
  background-size: cover !important;
}
</style>

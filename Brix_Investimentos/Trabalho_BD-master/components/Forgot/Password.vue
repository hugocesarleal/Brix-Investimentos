<template>
  <div>
    <!-- Página de Código de Segurança -->
    <div v-if="showSecurityCodePage && !showPasswordResetPage" class="security-code-container">
      <v-card class="elevation-12 rounded-xl">
        <v-toolbar dark color="#4A3AFF">
          <v-toolbar-title>Código de Segurança</v-toolbar-title>
        </v-toolbar>
        <v-sheet width="400" class="mx-5 my-5">
          <v-form @submit.prevent="handleVerifyCode">
            <v-text-field
              v-model="code"
              label="Código"
              outlined
              dense
            />
            <div class="button-group mt-4">
              <div class="d-flex">
                <v-btn small color="red" @click="goBack">Voltar</v-btn>
                <div style="width: 70px;"></div>  <v-btn small color="#4A3AFF" @click="handleVerifyCode">Verificar</v-btn>
            </div>
          </div>
            <div class="text-center mt-4">
              <v-btn text color="#4A3AFF" @click="resendCode">
                <v-icon left>mdi-restart</v-icon> Reenviar
              </v-btn>
            
            </div>
          </v-form>
        </v-sheet>
      </v-card>
    </div>

    <!-- Página de Redefinição de Senha -->
    <div v-else-if="showPasswordResetPage" class="security-code-container">
      <v-card class="elevation-12 rounded-xl">
        <v-toolbar dark color="#4A3AFF">
          <v-toolbar-title>Redefinir Senha</v-toolbar-title>
        </v-toolbar>
        <v-sheet width="400" class="mx-5 my-5">
          <v-form @submit.prevent="handleResetPassword">
            <v-text-field
              v-model="newPassword"
              label="Nova Senha"
              type="password"
              outlined
              dense
            />
            <v-text-field
              v-model="confirmPassword"
              label="Confirmar Senha"
              type="password"
              outlined
              dense
            />
            <div class="button-group mt-4">
              <v-btn small color="red" @click="goBackToSecurityCode">Voltar</v-btn>
              <div style="width: 10px;"></div><v-btn small color="#4A3AFF" @click="handleResetPassword">Alterar Senha</v-btn>
            </div>
          </v-form>
        </v-sheet>
      </v-card>
    </div>

    <!-- Página de Login -->
    <div v-else class="main-container">
      <v-container>
        <v-row class="fill-height">
          <!-- Coluna da esquerda com a logo e texto -->
          <v-col cols="12" md="6" class="left-column">
            <div class="branding">
              <img src="@/assets/image.png" alt="Logo" class="logo" />
              <div>
                <span class="brix">Brix</span>
                <span class="investimentos">Investimentos</span>
              </div>
            </div>
            <p class="description">
              Tire seus investimentos do piloto automático e leve-os para novas
              alturas com nosso aplicativo intuitivo. Explore oportunidades,
              acompanhe seus ganhos e transforme seus objetivos em realidade.
              Junte-se à revolução financeira hoje mesmo!
            </p>
            <div class="icon-wrapper">
              <v-icon class="icon">mdi-check-circle</v-icon>
              <span class="icon-text">100% gratuito e funcional</span>
            </div>
          </v-col>
          <!-- Coluna da direita com o formulário de login -->
          <v-col cols="12" md="6" class="form-column d-flex align-center justify-center">
            <v-card class="elevation-12 rounded-xl">
              <v-toolbar dark color="#4A3AFF">
                <v-toolbar-title>Encontre sua conta</v-toolbar-title>
              </v-toolbar>
              <v-sheet width="400" class="mx-5 my-5">
                <v-form fast-fail @submit.prevent="loading = true">
                  <input-email v-model="email" />
                  <button-dark
                    block
                    color="#4A3AFF"
                    text="Acessar"
                    :loading="loading"
                    @click="navigateToSecurityCode"
                  />
                </v-form>
              </v-sheet>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </div>
</template>


<script setup>
import { ref } from 'vue'

const email = ref('')
const loading = ref(false)
const code = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const showSecurityCodePage = ref(false)
const showPasswordResetPage = ref(false)
const router = useRouter(); // Access router instance


const stall = async (stallTime = 3000) => {
  await new Promise(resolve => setTimeout(resolve, stallTime))
}

const navigateToSecurityCode = async () => {
  loading.value = true
  await stall().then(() => {
    loading.value = false
    showSecurityCodePage.value = true
  })
}

const handleVerifyCode = () => {
  // Lógica para verificar o código
  // Se verificado com sucesso, mostre a página de redefinição de senha
  showPasswordResetPage.value = true
}

const handleResetPassword = () => {
  // Lógica para redefinir a senha
  if (newPassword.value === confirmPassword.value) {
    alert('Senha redefinida com sucesso!')
    // Redefina o estado para mostrar a página de login
    showSecurityCodePage.value = false
    showPasswordResetPage.value = false
    router.push('/login'); // Redirect to login route after reset
  } else {
    alert('As senhas não coincidem.')
  }
}

const goBack = () => {
  showSecurityCodePage.value = false
}

const goBackToSecurityCode = () => {
  showPasswordResetPage.value = false
}

const resendCode = () => {
  // Lógica para reenviar o código
  alert('Código reenviado!')
}
</script>


<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;800&display=swap');

.main-container {
  display: flex;
  margin-top: 150px;
}

.fill-height {
  height: 100%;
}

.left-column {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: center;
}

.form-column {
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.branding {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
}

.logo {
  height: 50px;
  width: 50px;
  margin-right: 7px;
}

.brix {
  color: black;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
  margin-right: 7px;
}

.investimentos {
  color: #4A3AFF;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
}

.description {
  font-size: 1.1rem;
  font-family: 'Montserrat', sans-serif;
  font-weight: 300;
  margin-top: 98px;
  text-align: center;
}

.icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 65px;
}

.icon {
  font-size: 1.5rem;
  color: #4A3AFF;
  margin-right: 0.5rem;
}

.icon-text {
  font-size: 1.1rem;
  color: #4A3AFF;
  font-family: 'Montserrat', sans-serif;
  font-weight: 800;
}

.security-code-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 70vh;
}

.button-group {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin: 0 auto;
}

.button-group v-btn {
  flex: 1;
}

.text-center {
  display: flex;
  justify-content: center;
}
</style>


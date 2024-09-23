<template>
  <div class="main-container">
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
              <v-toolbar-title>Login</v-toolbar-title>
            </v-toolbar>
            <v-sheet width="400" class="mx-5 my-5">
              <v-form @submit.prevent="handleLogin">
                <input-email v-model="email" :rules="emailRules" />
                <input-password v-model="password" :rules="passwordRules" />
                <button-dark
                  block
                  color="#4A3AFF"
                  text="Acessar"
                  :loading="loading"
                  :disabled="!isValidForm"
                  @click="handleLogin"
                />
                <!-- Link de recuperação de senha -->
                <p class="text-center mb-4">
                  <a href="/forgotPassword" class="forgot-password-link">Esqueceu sua senha?</a>
                </p>
              </v-form>
            </v-sheet>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const password = ref('')
const loading = ref(false)

const emailRules = [
  v => !!v || 'E-mail é obrigatório',
  v => /.+@.+/.test(v) || 'E-mail deve ser válido',
]

const passwordRules = [
  v => !!v || 'Senha é obrigatória',
]

const isValidForm = computed(() => !!email.value && !!password.value)

const handleLogin = async () => {
  if (!isValidForm.value) return

  loading.value = true
  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value,
    })
    localStorage.setItem('token', response.data.token)
    router.push('/relatorio')
  } catch (error) {
    console.error('Login failed:', error)
    alert('Login failed: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
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

.forgot-password-link {
  color: #5A56FB;
  cursor: pointer;
  font-size: 0.85rem;
}
</style>

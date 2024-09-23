<template>
  <div class="form-container">
    <v-card class="form-card">
      <v-form @submit.prevent="createAccount">
        <h2 class="form-title">Criar sua conta</h2>
        <p class="login-link">Já é usuário? <nuxt-link to="/login">Faça Login</nuxt-link></p>
        <v-row>
          <v-col cols="6">
            <v-text-field v-model="name" label="Nome" outlined required />
          </v-col>
          <v-col cols="6">
            <v-text-field v-model="sobrenome" label="Sobrenome" outlined required />
          </v-col>
        </v-row>
        <v-text-field v-model="email" label="E-mail" outlined :rules="emailRules" required />
        <v-text-field
          :type="showPassword ? 'text' : 'password'"
          v-model="password"
          label="Senha"
          outlined
          :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append="togglePassword"
          required
        />
        <v-text-field v-model="cpf" label="CPF" outlined :rules="cpfRules" required />
        <v-text-field v-model="celular" label="Celular" outlined required />
        <div class="form-buttons">
          <v-btn class="custom-button" color="#66DD99" type="submit" :loading="loading">
            Criar Conta
          </v-btn>
        </div>
      </v-form>
    </v-card>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const name = ref('')
const sobrenome = ref('')
const email = ref('')
const password = ref('')
const cpf = ref('')
const celular = ref('')
const showPassword = ref(false)
const loading = ref(false)

const router = useRouter()

const apiBase = process.env.apiBase || 'http://localhost:8000'

const emailRules = [
  (v) => !!v || 'E-mail é obrigatório',
  (v) => /.+@.+\..+/.test(v) || 'E-mail deve ser válido',
]

const cpfRules = [
  (v) => !!v || 'CPF é obrigatório',
  (v) => /^\d{3}\.\d{3}\.\d{3}\-\d{2}$/.test(v) || 'CPF deve ser válido (ex: 123.456.789-00)',
]

const createAccount = async () => {
  if (name.value && sobrenome.value && email.value && password.value && cpf.value && celular.value) {
    loading.value = true
    const requestData = {
      name: name.value,
      sobrenome: sobrenome.value,
      email: email.value,
      password: password.value,
      password_confirmation: password.value, // Certifique-se de enviar password_confirmation se necessário
      cpf: cpf.value,
      celular: celular.value,
    }
    console.log('Dados enviados:', requestData) // Log dos dados enviados
    try {
      const response = await axios.post(`${apiBase}/api/register`, requestData)
      alert('Conta criada com sucesso!')
      router.push('/login')
    } catch (error) {
      console.error('Erro ao criar conta:', error.response?.data)
      alert('Erro ao criar conta: ' + (error.response?.data?.message || error.message))
    } finally {
      loading.value = false
    }
  } else {
    alert('Por favor, preencha todos os campos obrigatórios.')
  }
}


const togglePassword = () => {
  showPassword.value = !showPassword.value
}
</script>

<style scoped>
.form-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 70vh;
  padding: 20px;
  border: 2px solid #4A3AFF;
  border-radius: 15px;
  background-color: #FFFFFF;
}

.form-card {
  width: 450px;
  background-color: #FFFFFF;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
}

.form-title {
  margin-bottom: 20px;
  text-align: center;
}

.login-link {
  text-align: center;
  margin-bottom: 20px;
}

.form-buttons {
  display: flex;
  justify-content: center;
}

.custom-button {
  width: 48%;
  font-weight: bold;
  border-radius: 25px;
  box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
  color: #FFFFFF;
}
</style>

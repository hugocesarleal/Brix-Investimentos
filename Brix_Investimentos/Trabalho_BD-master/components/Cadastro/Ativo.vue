<template>
  <div>
    <v-app-bar fixed color="#041022" dark>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title class="mr-6">Brix Investimentos</v-app-bar-title>
      <v-btn @click="toggleView" text>
        {{ showForm ? 'Ver Ativos Cadastrados' : 'Cadastrar Ativo' }}
      </v-btn>
      <v-icon @click="print" class="ml-auto">mdi-printer</v-icon>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" temporary clipped app>
      <v-list dense>
        <v-list-item v-for="item in items" :key="item.text" :to="item.link">
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>{{ item.text }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <template v-if="showForm">
        <v-card class="pa-4">
          <v-card-title class="headline">Cadastrar Ativo</v-card-title>
          <v-card-text>
            <v-form ref="form" v-model="valid" lazy-validation>
              <v-text-field
                label="Nome do Ativo"
                v-model="ativo.nome"
                :rules="[rules.required]"
                required
              ></v-text-field>
              <v-text-field
                label="Ticker"
                v-model="ativo.ticker"
                :rules="[rules.required]"
                required
              ></v-text-field>
              <v-text-field
                label="Setor"
                v-model="ativo.setor"
                :rules="[rules.required]"
                required
              ></v-text-field>
              <v-text-field
                label="Indústria"
                v-model="ativo.industria"
                :rules="[rules.required]"
                required
              ></v-text-field>
              <v-text-field
                label="Quantidade"
                v-model="ativo.quantidade"
                type="number"
                :rules="[rules.required]"
                required
              ></v-text-field>
              <v-text-field
                label="Preço de Compra"
                v-model="ativo.preco_compra"
                type="number"
                :rules="[rules.required]"
                prefix="R$ "
                required
              ></v-text-field>
              <v-textarea label="Observações" v-model="ativo.observacoes"></v-textarea>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-btn color="#4A3AFF" @click="submit" :disabled="!valid">Salvar</v-btn>
            <v-btn color="#FF5252" @click="resetForm">Cancelar</v-btn>
          </v-card-actions>
        </v-card>
      </template>
      <template v-else>
        <v-card v-if="ativos.length > 0" class="pa-4 mb-4" v-for="(ativo, index) in ativos" :key="index">
          <v-card-title class="headline">{{ ativo.nome }}</v-card-title>
          <v-card-text>
            <p><strong>Ticker:</strong> {{ ativo.ticker }}</p>
            <p><strong>Setor:</strong> {{ ativo.setor }}</p>
            <p><strong>Indústria:</strong> {{ ativo.industria }}</p>
            <p><strong>Preço de Compra:</strong> {{ formatCurrency(ativo.preco_compra) }}</p>
            <p v-if="ativo.observacoes"><strong>Observações:</strong> {{ ativo.observacoes }}</p>
          </v-card-text>
        </v-card>
        <v-alert v-else>
          Nenhum ativo cadastrado.
        </v-alert>
      </template>
    </v-main>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      drawer: false,
      showForm: true,
      valid: false,
      items: [
        { text: 'Cadastrar Ativos', icon: 'mdi-wallet-bifold', link: '/cadastro' },
        { text: 'Histórico de Preços Ativos', icon: 'mdi-chart-bar', link: '/historico' },
        { text: 'Compra e Venda', icon: 'mdi-currency-usd', link: '/compra_venda' },
        { text: 'Relatório', icon: 'mdi-finance', link: '/relatorio' },
      ],
      ativos: [],
      ativo: {
        nome: '',
        ticker: '',
        setor: '',
        industria: '',
        quantidade: null,
        preco_compra: null,
        observacoes: '',
      },
      rules: {
        required: value => !!value || 'Campo obrigatório',
      },
    };
  },
  mounted() {
    this.fetchAtivos();
  },
  methods: {
    print() {
      window.print();
    },
    async submit() {
      if (this.$refs.form.validate()) {
        try {
          console.log('Dados do ativo antes do envio:', this.ativo); // Log para verificar os dados
          const token = localStorage.getItem('token');
          const response = await axios.post('http://localhost:8000/api/v1/cadastrar-ativos', this.ativo, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });
          this.resetForm();
          this.fetchAtivos();
          console.log(response.data);
        } catch (error) {
          console.error('Erro ao cadastrar ativo:', error.response.data);
        }
      }
    },
    async fetchAtivos() {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://localhost:8000/api/v1/cadastrar-ativos', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.ativos = response.data;
      } catch (error) {
        console.error('Erro ao buscar ativos:', error.response.data.message);
      }
    },
    resetForm() {
      this.$refs.form.reset();
      this.$refs.form.resetValidation();
    },
    toggleView() {
      this.showForm = !this.showForm;
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
    },
  },
};
</script>

<style scoped>
.headline {
  font-weight: bold;
  font-size: 1.5em;
}
.pa-4 {
  padding: 1rem;
}
</style>

<template>
  <div>
    <v-app-bar fixed color="#041022" dark>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title class="mr-6">Brix Investimentos</v-app-bar-title>
      <v-btn @click="openFilePicker" accept="application/pdf" hide-details class="mr-4">
        Carregar
      </v-btn>
      <v-menu open-on-hover v-for="(options, label) in dropdownOptions" :key="label">
        <template v-slot:activator="{ props }">
          <v-btn color="primary" v-bind="props" class="mr-4">
            {{ label }}: {{ selected[label.toLowerCase()] }}
          </v-btn>
        </template>
        <v-list>
          <v-list-item v-for="option in options" :key="option" @click="selectDropdown(label.toLowerCase(), option)">
            <v-list-item-title>{{ option }}</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <v-icon @click="print" class="ml-6">mdi-printer</v-icon>
      <input ref="fileInput" type="file" style="display: none;" accept="application/pdf" @change="handleFileUpload" />
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" temporary clipped app>
      <v-list dense>
        <v-list-item v-for="item in items" :key="item.text" :to="{ path: item.link }">
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
      <v-container>
        <!-- Mensagem de Erro -->
        <v-alert v-if="error" type="error" dismissible>
          {{ error }}
        </v-alert>

        <!-- Cards de informações -->
        <v-row>
          <v-col cols="12" md="6" lg="4" v-for="card in infoCards" :key="card.title">
            <v-card class="pa-4">
              <div>
                <h3>{{ card.title }}</h3>
                <p>{{ card.value }}</p>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Gráficos -->
        <v-row>
          <v-col cols="12" md="6">
            <v-card class="pa-4">
              <canvas ref="lineChartCanvas"></canvas>
            </v-card>
          </v-col>
          <v-col cols="12" md="6">
            <v-card class="pa-4">
              <canvas ref="barChartCanvas"></canvas>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';
import axios from 'axios';

export default defineComponent({
  name: 'Grafico',
  data() {
    return {
      drawer: false,
      dropdownOptions: {
        Início: ["Hoje", "Ontem", "Semana Passada", "Mês Passado"],
        Período: ["1 mês", "3 meses", "6 meses", "1 ano"],
        Conta: ["Todas as contas", "Conta específica 1", "Conta específica 2", "Conta específica 3"],
        'Estratégia de Alocação': ["Ações", "Renda Fixa", "Fundos Imobiliários"],
        'Grupo de Estratégia': ["Ticker ABC", "Ticker XYZ", "Ticker QRS"],
      },
      selected: {
        inicio: "Hoje",
        periodo: "6 meses",
        conta: "Todas as contas",
        estrategia: "Ações",
        grupo: "Ticker ABC",
      },
      items: [
        { text: 'Cadastrar Ativos', icon: 'mdi-wallet-bifold', link: '/cadastro' },
        { text: 'Histórico de Preços Ativos', icon: 'mdi-chart-bar', link: '/historico' },
        { text: 'Compra e Venda', icon: 'mdi-currency-usd', link: '/compra_venda' },
        { text: 'Relatório', icon: 'mdi-finance', link: '/relatorio' },
      ],
      infoCards: [
        { title: 'Posição Inicial', value: '' },
        { title: 'Posição Final', value: '' },
        { title: 'Movimentação', value: '' },
        { title: 'Rentabilidade', value: '' },
        { title: 'Volatilidade', value: '' },
        { title: 'Resultado Projetado', value: '' }
      ],
      rentabilidadeData: {
        labels: [],
        datasets: [
          {
            label: 'Rentabilidade',
            backgroundColor: '#42A5F5',
            data: []
          }
        ]
      },
      rentabilidadeMensalData: {
        labels: [],
        datasets: [
          {
            label: 'Rentabilidade Mensal',
            backgroundColor: '#FF6384',
            data: []
          }
        ]
      },
      chartOptions: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function (tooltipItem) {
                return tooltipItem.label + ': ' + tooltipItem.raw + '%';
              }
            }
          }
        }
      },
      error: null, // Adicionar uma variável para mensagens de erro
    };
  },
  methods: {
    openFilePicker() {
      this.$refs.fileInput.click();
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file && file.type === 'application/pdf') {
        console.log('Arquivo carregado:', file);
      } else {
        console.error('Por favor, selecione um arquivo PDF válido.');
      }
    },
    print() {
      window.print();
    },
    selectDropdown(type, option) {
      this.selected[type] = option;
      this.fetchData();
    },
    fetchData() {
      // Suponha que o token JWT esteja armazenado no localStorage
      const token = localStorage.getItem('token');

      axios.get('http://localhost:8000/api/v1/relatorio-diversos', {
        params: this.selected,
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
        .then(response => {
          const data = response.data;
          this.infoCards[0].value = data.posicao_inicial;
          this.infoCards[1].value = data.posicao_final;
          this.infoCards[2].value = data.movimentacao;
          this.infoCards[3].value = data.rentabilidade_periodo;
          this.infoCards[4].value = data.volatilidade;
          this.infoCards[5].value = data.resultado_projetado;

          this.rentabilidadeData.labels = data.rentabilidade.labels;
          this.rentabilidadeData.datasets[0].data = data.rentabilidade.data;

          this.rentabilidadeMensalData.labels = data.rentabilidade_mensal.labels;
          this.rentabilidadeMensalData.datasets[0].data = data.rentabilidade_mensal.data;

          this.renderCharts();
        })
        .catch(error => {
          this.error = 'Erro ao buscar os dados.';
          console.error('Erro ao buscar os dados:', error);
        });
    },
    renderCharts() {
      // Limpa os gráficos existentes, se necessário
      const ctxLine = this.$refs.lineChartCanvas.getContext('2d');
      if (this.lineChart) this.lineChart.destroy(); // Remove o gráfico existente, se houver
      this.lineChart = new Chart(ctxLine, {
        type: 'line',
        data: this.rentabilidadeData,
        options: this.chartOptions
      });

      const ctxBar = this.$refs.barChartCanvas.getContext('2d');
      if (this.barChart) this.barChart.destroy(); // Remove o gráfico existente, se houver
      this.barChart = new Chart(ctxBar, {
        type: 'bar',
        data: this.rentabilidadeMensalData,
        options: this.chartOptions
      });
    }
  },
  mounted() {
    this.fetchData();
  }
});
</script>

<style scoped>
.v-app-bar {
  background-color: #041022;
}
</style>
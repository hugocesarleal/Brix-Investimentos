<template>
  <div>
    <!-- Barra de Aplicativo -->
    <v-app-bar fixed color="#041022" dark>
      <v-app-bar-nav-icon @click="toggleDrawer"></v-app-bar-nav-icon>
      <v-app-bar-title>Histórico de Preços de Ativos</v-app-bar-title>
      <v-icon @click="print" class="ml-auto">mdi-printer</v-icon>
    </v-app-bar>

    <!-- Menu de Navegação -->
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

    <!-- Conteúdo Principal -->
    <v-main>
      <v-container>
        <div class="column-descriptions">
          <div v-for="column in headers" :key="column.text" class="column-description">
            <span class="column-description-text">{{ column.text }}</span>
          </div>
        </div>

        <v-data-table
          v-if="!loading && prices.length > 0"
          :headers="headers"
          :items="prices"
          item-key="id"
          class="elevation-1"
        >
          <template v-slot:item="{ item }">
            <tr class="bordered-row">
              <td v-for="(header, index) in headers" :key="index" class="cell-center">
                <span :class="header.class">{{ formatValue(item[header.value], header.type) }}</span>
              </td>
            </tr>
          </template>
        </v-data-table>
        <div v-else-if="loading" class="loading-message">Carregando dados...</div>
        <div v-else class="loading-message">Nenhum dado disponível.</div>
      </v-container>
    </v-main>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      drawer: false,
      loading: false,
      prices: [],
      items: [
        { text: 'Cadastrar Ativos', icon: 'mdi-wallet-bifold', link: '/cadastro' },
        { text: 'Histórico de Preços Ativos', icon: 'mdi-chart-bar', link: '/historico' },
        { text: 'Compra e Venda', icon: 'mdi-currency-usd', link: '/compra_venda' },
        { text: 'Relatório', icon: 'mdi-finance', link: '/relatorio' },
      ],
      headers: [
        { text: 'Ticker', value: 'ticker', class: 'ticker-column', type: 'text' },
        { text: 'Data', value: 'data_ativo', class: 'date-column', type: 'date' },
        { text: 'Abertura', value: 'open', class: 'open-column', type: 'currency' },
        { text: 'Mínimo', value: 'low', class: 'low-column', type: 'currency' },
        { text: 'Máximo', value: 'high', class: 'high-column', type: 'currency' },
        { text: 'Fechamento', value: 'close', class: 'close-column', type: 'currency' },
        { text: 'Volume', value: 'volume', class: 'volume-column', type: 'number' }
      ],
      interval: 60000, // Intervalo de 1 minuto
      refreshInterval: null
    };
  },
  mounted() {
    this.fetchData(); // Buscar dados imediatamente ao montar
    this.refreshInterval = setInterval(this.fetchData, this.interval); // Atualiza os dados a cada minuto
  },
  beforeUnmount() {
    clearInterval(this.refreshInterval); // Limpa o intervalo ao desmontar o componente
  },
  methods: {
    async fetchData() {
      this.loading = true;
      const token = localStorage.getItem('token'); // Recupera o token do localStorage
      try {
        const response = await axios.get('http://localhost:8000/api/v1/get-stock-history', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        this.prices = this.processData(response.data); // Atualiza a lista com os dados mais recentes
      } catch (error) {
        console.error("Erro ao buscar dados:", error.response ? error.response.data : error.message);
        this.prices = []; // Limpa os dados em caso de erro
      } finally {
        this.loading = false; // Sempre define loading como false
      }
    },
    processData(data) {
      const now = new Date();
      const currentMinute = now.getMinutes();
      
      // Filtra dados com base na hora atual
      const filteredData = data.filter(item => {
        const itemDate = new Date(item.data_ativo);
        return itemDate.getMinutes() === currentMinute;
      });

      // Ordena os dados por ticker e por data
      filteredData.sort((a, b) => new Date(a.data_ativo) - new Date(b.data_ativo));

      // Limita a 20 tickers únicos e retorna os dados processados
      return filteredData.slice(0, 20);
    },
    print() {
      window.print();
    },
    formatValue(value, type) {
      switch (type) {
        case 'currency':
          return `R$ ${parseFloat(value).toFixed(2)}`;
        case 'date':
          return new Date(value).toLocaleString('pt-BR'); // Formata a data para o padrão brasileiro
        case 'number':
          return parseInt(value).toLocaleString('pt-BR'); // Formata o número para o padrão brasileiro
        default:
          return value;
      }
    },
    toggleDrawer() {
      this.drawer = !this.drawer;
    }
  }
};
</script>

<style scoped>
.elevation-1 {
  background-color: #c0d5e2;
  border-radius: 8px;
  overflow: hidden;
}

.column-descriptions {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  background-color: #041022;
  margin-bottom: 10px;
}

.column-description {
  flex: 1;
  text-align: center;
}

.column-description-text {
  font-weight: bold;
  color: white;
}

.bordered-row {
  border-bottom: 1px solid #0d0344;
}

.cell-center {
  text-align: center;
}

.ticker-column {
  color: #FF4081;
  font-weight: bold;
}

.date-column {
  color: #3F51B5;
}

.open-column {
  color: #8BC34A;
}

.low-column {
  color: #FFC107;
}

.high-column {
  color: #F44336;
}

.close-column {
  color: #9C27B0;
}

.volume-column {
  color: #00BCD4;
}

.loading-message {
  text-align: center;
  font-size: 1.5rem;
}

.v-data-table-header {
  background-color: #041022 !important;
  color: white !important;
}

.v-data-table th {
  color: white !important;
  font-weight: bold;
}

.v-data-table td {
  background-color: white;
  color: black;
}
</style>

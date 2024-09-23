<template>
  <!-- Restante do template permanece igual -->
  <v-app-bar fixed color="#041022" dark>
    <v-app-bar-nav-icon @click="toggleDrawer"></v-app-bar-nav-icon>
    <v-app-bar-title class="mr-6">Compra e Venda de Ativos</v-app-bar-title>
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

  <!-- Main Content -->
  <v-main>
    <v-container>
      <!-- Ativos Disponíveis e Detalhes do Ativo -->
      <v-row>
        <v-col cols="12" md="6">
          <v-card class="mb-4">
            <v-card-title class="primary white--text">Ativos Disponíveis</v-card-title>
            <v-card-text>
              <v-data-table :headers="headers" :items="ativos" item-key="ticker" class="elevation-1">
                <template v-slot:item="{ item }">
                  <tr @click="selectAtivo(item)" class="cursor-pointer">
                    <td class="text-start">{{ item.ticker }}</td>
                    <td>{{ item.nome }}</td>
                    <td class="text-end">{{ formatValue(item.precoAtual, 'currency') }}</td>
                  </tr>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="6">
          <v-card class="mb-4" v-if="ativoSelecionado">
            <v-card-title class="accent white--text">Detalhes do Ativo</v-card-title>
            <v-card-text>
              <div>
                <span class="info-label">Ticker:</span> {{ ativoSelecionado.ticker }}
              </div>
              <div>
                <span class="info-label">Nome:</span> {{ ativoSelecionado.nome }}
              </div>
              <div>
                <span class="info-label">Tipo:</span> {{ ativoSelecionado.tipo }}
              </div>
              <div>
                <span class="info-label">Preço Atual:</span> {{ formatValue(ativoSelecionado.precoAtual, 'currency') }}
              </div>
              <div v-if="ativoSelecionado.setor">
                <span class="info-label">Setor:</span> {{ ativoSelecionado.setor }}
              </div>
              <div v-if="ativoSelecionado.industria">
                <span class="info-label">Indústria:</span> {{ ativoSelecionado.industria }}
              </div>
            </v-card-text>
            <v-card-actions>
              <v-btn color="primary darken-2" @click="openForm('compra')">Comprar</v-btn>
              <v-btn color="error darken-2" @click="openForm('venda')">Vender</v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
      <!-- Formulário de Compra/Venda -->
      <v-dialog v-model="dialog" max-width="600">
        <v-card>
          <v-card-title>{{ formMode === 'compra' ? 'Compra de Ativo' : 'Venda de Ativo' }}</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="submitAporte">
              <v-text-field v-model="form.quantidade" label="Quantidade" type="number" required
                :error-messages="formErrors.quantidade"></v-text-field>
              <v-text-field v-model="form.precoUnitario" label="Preço Unitário" type="number" required
                :error-messages="formErrors.precoUnitario"></v-text-field>
              <v-btn type="submit" color="primary">Confirmar</v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-dialog>
      <!-- Compras e Vendas Realizadas -->
      <v-card class="mb-4">
        <v-card-title class="accent white--text">Minhas Compras e Vendas</v-card-title>
        <v-card-text>
          <v-data-table :headers="transacaoHeaders" :items="transacoes" class="elevation-1">
            <template v-slot:item="{ item }">
              <tr>
                <td>{{ item.tipo }}</td>
                <td>{{ item.ticker }}</td>
                <td>{{ item.quantidade }}</td>
                <td>{{ formatValue(item.precoUnitario, 'currency') }}</td>
                <td>{{ formatValue(item.precoAtual, 'currency') }}</td>
                <td>{{ formatValue(item.valorTotal, 'currency') }}</td>
                <td>{{ formatValue(item.data, 'date') }}</td>
              </tr>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-container>
  </v-main>
</template>
<!-- Resto do conteúdo permanece igual -->


<script>
import axios from 'axios';

export default {
  data() {
    return {
      drawer: false,
      ativos: [],
      items: [
        { text: 'Cadastrar Ativos', icon: 'mdi-wallet-bifold', link: '/cadastro' },
        { text: 'Histórico de Preços', icon: 'mdi-chart-bar', link: '/historico' },
        { text: 'Relatório', icon: 'mdi-finance', link: '/relatorio' }
      ],
      headers: [
        { text: 'Ticker', value: 'ticker' },
        { text: 'Nome', value: 'nome' },
        { text: 'Preço Atual', value: 'precoAtual' }
      ],
      transacaoHeaders: [
        { text: 'Tipo', value: 'tipo' },
        { text: 'Ticker', value: 'ticker' },
        { text: 'Quantidade', value: 'quantidade' },
        { text: 'Preço Unitário', value: 'precoUnitario' },
        { text: 'Preço Atual', value: 'precoAtual' },
        { text: 'Valor Total', value: 'valorTotal' },
        { text: 'Data', value: 'data' }
      ],
      ativoSelecionado: null,
      dialog: false,
      formMode: '',
      form: {
        quantidade: null,
        precoUnitario: null
      },
      formErrors: {
        quantidade: '',
        precoUnitario: ''
      },
      transacoes: []
    };
  },
  async mounted() {
    await this.fetchAtivos();
    await this.fetchTransacoes();
    this.startAtivosUpdateInterval();
  },
  methods: {
    toggleDrawer() {
      this.drawer = !this.drawer;
    },
    async fetchAtivos() {
      const token = localStorage.getItem('token');
      console.log('Buscando ativos...');
      try {
        const response = await axios.get('http://localhost:8000/api/v1/cadastrar-ativos', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        this.ativos = response.data.map(item => ({
          ...item,
          precoAtual: parseFloat(item.precoAtual) || 0
        }));

        this.ativosMap = this.ativos.reduce((map, item) => {
          map[item.ticker] = item.id;
          return map;
        }, {});
      } catch (error) {
        console.error('Erro ao buscar ativos:', error);
      }
    },
    async fetchTransacoes() {
      const token = localStorage.getItem('token');
      try {
        const [comprasResponse, vendasResponse] = await Promise.all([
          axios.get('http://localhost:8000/api/v1/compras', {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          }),
          axios.get('http://localhost:8000/api/v1/vendas', {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          })
        ]);

        const formatTransacoes = (items, tipo) => items.map(item => ({
          ...item,
          tipo,
          valorTotal: parseFloat(item.valor_total) || 0,
          precoUnitario: parseFloat(item.valor_unitario) || 0,
          precoAtual: this.ativos.find(ativo => ativo.ticker === item.ticker)?.precoAtual || 0,
          data: item.created_at ? new Date(item.created_at).toLocaleDateString('pt-BR') : ''
        }));

        const compras = formatTransacoes(comprasResponse.data, 'Compra');
        const vendas = formatTransacoes(vendasResponse.data, 'Venda');

        this.transacoes = [...compras, ...vendas].sort((a, b) => new Date(b.data) - new Date(a.data));
      } catch (error) {
        console.error('Erro ao buscar transações:', error);
      }
    },
    async selectAtivo(item) {
      this.ativoSelecionado = item;
      this.dialog = true;
    },
    openForm(mode) {
      this.formMode = mode;
      this.dialog = true;
      this.formErrors = {}; // Resetar erros
    },
    async submitAporte() {
      const token = localStorage.getItem('token');
      const endpoint = this.formMode === 'compra'
        ? 'http://localhost:8000/api/v1/compras'
        : 'http://localhost:8000/api/v1/vendas';

      const idTicker = this.ativosMap[this.ativoSelecionado.ticker];

      if (!idTicker) {
        console.error('ID do ticker é inválido:', this.ativoSelecionado.ticker);
        alert('ID do ticker é inválido.');
        return;
      }

      const payload = {
        id_ticker: idTicker,
        quantidade: parseFloat(this.form.quantidade),
        valor_unitario: parseFloat(this.form.precoUnitario),
        valor_total: parseFloat(this.form.precoUnitario) * parseFloat(this.form.quantidade)
      };

      console.log(`Enviando ${this.formMode} com dados:`, payload);

      try {
        const response = await axios.post(endpoint, payload, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        console.log(`${this.formMode.charAt(0).toUpperCase() + this.formMode.slice(1)} realizado com sucesso.`);
        this.dialog = false;
        this.form.quantidade = null;
        this.form.precoUnitario = null;
        await this.fetchAtivos();
        await this.fetchTransacoes();
      } catch (error) {
        console.error(`Erro ao realizar ${this.formMode}:`, error.response ? error.response.data : error.message);
        alert(`Erro ao realizar ${this.formMode}.`);
        if (error.response && error.response.data) {
          this.formErrors = error.response.data.errors || {};
        }
      }
    },
    formatValue(value, type) {
      if (type === 'currency') {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
      } else if (type === 'date') {
        return new Date(value).toLocaleDateString('pt-BR');
      }
      return value;
    },
    startAtivosUpdateInterval() {
      setInterval(async () => {
        await this.fetchAtivos();
      }, 60000);
    },
    print() {
      window.print();
    }
  }
}
</script>

<style scoped>
.v-data-table {
  font-size: 14px;
}

.info-label {
  font-weight: bold;
}

.primary {
  background-color: #1976D2 !important;
}

.accent {
  background-color: #FF5722 !important;
}

.cursor-pointer {
  cursor: pointer;
}
</style>


<!-- <style scoped>
.v-data-table {
  font-size: 14px;
}

.info-label {
  font-weight: bold;
}

.primary {
  background-color: #1976D2 !important;
}

.accent {
  background-color: #FF5722 !important;
}

.cursor-pointer {
  cursor: pointer;
}
</style> -->


<!-- <style scoped>
.info-label {
  font-weight: bold;
  color: #333;
}
.cursor-pointer {
  cursor: pointer;
}
.primary {
  background-color: #041022;
}
.accent {
  background-color: #FFC107;
}
.white--text {
  color: #ffffff;
}
</style> -->

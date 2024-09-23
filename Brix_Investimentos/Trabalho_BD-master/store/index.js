// store/index.js

import { createStore } from 'vuex';
import axios from 'axios';

const store = createStore({
  state: {
    user: null,
    ativos: [],
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setAtivos(state, ativos) {
      state.ativos = ativos;
    },
    addAtivo(state, ativo) {
      state.ativos.push(ativo);
    },
    removeAtivo(state, ativoId) {
      state.ativos = state.ativos.filter(ativo => ativo.id !== ativoId);
    },
  },
  actions: {
    async fetchAtivos({ commit }) {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get('http://localhost:8000/api/v1/cadastrar-ativos', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        commit('setAtivos', response.data);
      } catch (error) {
        console.error('Erro ao buscar ativos:', error);
      }
    },
    async cadastrarAtivo({ commit, state }, novoAtivo) {
      try {
        const token = localStorage.getItem('token');
        const response = await axios.post('http://localhost:8000/api/v1/cadastrar-ativos', novoAtivo, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        commit('addAtivo', response.data);
      } catch (error) {
        console.error('Erro ao cadastrar ativo:', error);
        throw error;
      }
    },
    // Adicione outras actions conforme necessário
  },
});

export default store;

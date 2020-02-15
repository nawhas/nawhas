import client from '../../services/client';

const state = {
  reciters: null,
  reciter: null,
  recitersPaginationLength: null,
};

const getters = {
  reciters: (state) => state.reciters,
  reciter: (state) => state.reciter,
  recitersPaginationLength: (state) => state.recitersPaginationLength,
};

const mutations = {
  FETCH_RECITERS(state, payload) {
    state.reciters = payload.data;
  },
  SET_RECITERS_PAGINATION_LENGTH(state, payload) {
    state.recitersPaginationLength = payload.data;
  },
  FETCH_RECITER(state, payload) {
    state.reciter = payload.data;
  },
  STORE_RECITER(state, payload) {
    state.reciters.push(payload.data);
  },
  UPDATE_RECITER(state, payload) {
    state.reciter = payload.data;
  },
};

const actions = {
  async fetchReciters({ commit }, payload) {
    const response = await client.get('/v1/reciters', { page: payload.page });
    commit('FETCH_RECITERS', {
      data: response.data.data,
    });
    commit('SET_RECITERS_PAGINATION_LENGTH', {
      data: response.data.meta.pagination.total_pages,
    });
  },
  async fetchReciter({ commit }, payload) {
    const response = await client.get(`/v1/reciters/${payload.reciter}`);
    commit('FETCH_RECITER', {
      data: response.data,
    });
  },
  async storeReciter({ commit }, payload) {
    const response = await client.post('/v1/reciters', payload.form);
    commit('STORE_RECITER', {
      data: response.data.data,
    });
  },
  async updateReciter({ commit }, payload) {
    const response = await client.post(`/v1/reciters/${payload.reciter}`, payload.form);
    commit('UPDATE_RECITER', {
      data: response.data.data,
    });
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
  namespaced: true,
};

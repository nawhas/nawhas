import client from '@/services/client';

const state = {
  languages: null,
};

const getters = {
  languages: (state) => state.languages,
};

const mutations = {
  FETCH_LANGUAGES(state, payload) {
    state.languages = payload.data;
  },
};

const actions = {
  async fetchLanguages({ commit }) {
    const response = await client.get('/v1/languages');
    commit('FETCH_LANGUAGES', {
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

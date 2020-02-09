import client from '../../services/client';

const state = {
  lyric: null,
};

const getters = {
  lyric: (state) => state.lyric,
};

const mutations = {
  FETCH_LYRIC(state, payload) {
    state.lyric = payload.data;
  },
  STORE_LYRIC(state, payload) {
    state.lyric = payload.data;
  },
  UPDATE_LYRIC(state, payload) {
    state.lyric = payload.data;
  },
};

const actions = {
  async fetchLyric({ commit }, payload) {
    const response = await client.get(
      `/v1/reciters/${payload.reciter}/albums/${payload.album}/tracks/${payload.track}/lyrics/${payload.lyric}`,
    );
    commit('FETCH_LYRIC', {
      data: response.data,
    });
  },
  async storeLyric({ commit }, payload) {
    const response = await client.post(
      `/v1/reciters/${payload.reciter}/albums/${payload.album}/tracks/${payload.track}/lyrics`,
      payload.form,
    );
    commit('STORE_LYRIC', {
      data: response.data,
    });
  },
  async updateLyric({ commit }, payload) {
    const response = await client.post(
      `/v1/reciters/${payload.reciter}/albums/${payload.album}/tracks/${payload.track}/lyrics/${payload.lyric}`,
      payload.form,
    );
    commit('UPDATE_LYRIC', {
      data: response.data,
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

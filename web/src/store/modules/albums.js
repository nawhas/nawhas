import client from '@/services/client';

const state = {
  albums: null,
  album: null,
};

const getters = {
  albums: (state) => state.albums,
  album: (state) => state.album,
};

const mutations = {
  FETCH_ALBUMS(state, payload) {
    state.albums = payload.data;
  },
  FETCH_ALBUM(state, payload) {
    state.album = payload.data;
  },
  STORE_ALBUM(state, payload) {
    state.album = payload.data;
  },
  UPDATE_ALBUM(state, payload) {
    state.album = payload.data;
  },
};

const actions = {
  async fetchAlbums({ commit }, payload) {
    const response = await client.get(`/v1/reciters/${payload.reciter}/albums?include=tracks&per_page=100`);
    commit('FETCH_ALBUMS', {
      data: response.data.data,
    });
  },
  async fetchAlbum({ commit }, payload) {
    const response = await client.get(`/v1/reciters/${payload.reciter}/albums/${payload.album}`);
    commit('FETCH_ALBUM', {
      data: response.data,
    });
  },
  async storeAlbum({ commit }, payload) {
    const response = await client.post(`/v1/reciters/${payload.reciter}/albums`, payload.form);
    commit('STORE_ALBUM', {
      data: response.data,
    });
  },
  async updateAlbum({ commit }, payload) {
    const response = await client.post(`/v1/reciters/${payload.reciter}/albums/${payload.album}`, payload.form);
    commit('UPDATE_ALBUM', {
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

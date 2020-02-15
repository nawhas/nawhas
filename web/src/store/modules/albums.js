import client from '@/services/client';

const state = {
  albums: null,
  album: null,
  albumsPaginationLength: null,
};

const getters = {
  albums: (state) => state.albums,
  album: (state) => state.album,
  albumsPaginationLength: (state) => state.albumsPaginationLength,
};

const mutations = {
  FETCH_ALBUMS(state, payload) {
    state.albums = payload.data;
  },
  SET_ALBUMS_PAGINATION_LENGTH(state, payload) {
    state.albumsPaginationLength = payload.data;
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
    const response = await client.get(`/v1/reciters/${payload.reciter}/albums?include=tracks`, { page: payload.page });
    commit('FETCH_ALBUMS', {
      data: response.data.data,
    });
    commit('SET_ALBUMS_PAGINATION_LENGTH', {
      data: response.data.meta.pagination.total_pages,
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

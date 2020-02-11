import client from '../../services/client';

const state = {
  popularReciters: null,
  popularAlbums: null,
  popularTracks: null,
};

const getters = {
  popularReciters: (state) => state.popularReciters,
  popularAlbums: (state) => state.popularAlbums,
  popularTracks: (state) => state.popularTracks,
};

const mutations = {
  FETCH_POPULAR_RECITERS(state, payload) {
    state.popularReciters = payload.data;
  },
  FETCH_POPULAR_ALBUMS(state, payload) {
    state.popularAlbums = payload.data;
  },
  FETCH_POPULAR_TRACKS(state, payload) {
    state.popularTracks = payload.data;
  },
};

const actions = {
  async fetchPopularReciters({ commit }, options = {}) {
    const response = await client.get('/v1/popular/reciters', options);
    commit('FETCH_POPULAR_RECITERS', {
      data: response.data.data,
    });
  },
  async fetchPopularAlbums({ commit }, options = {}) {
    const response = await client.get('/v1/popular/albums', options);
    commit('FETCH_POPULAR_ALBUMS', {
      data: response.data.data,
    });
  },
  async fetchPopularTracks({ commit }, options = {}) {
    const response = await client.get('/v1/popular/tracks?include=album,reciter', options);
    commit('FETCH_POPULAR_TRACKS', {
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

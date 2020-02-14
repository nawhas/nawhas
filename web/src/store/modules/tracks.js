import store from '@/store';
import client from '../../services/client';

const state = {
  tracks: null,
  track: null,
};

const getters = {
  tracks: (state) => state.tracks,
  track: (state) => state.track,
};

const mutations = {
  FETCH_TRACKS(state, payload) {
    state.tracks = payload.data;
  },
  FETCH_TRACK(state, payload) {
    state.track = payload.data;
  },
  STORE_TRACK(state, payload) {
    state.track = payload.data;
  },
  UPDATE_TRACK(state, payload) {
    state.track = payload.data;
  },
};

const actions = {
  async fetchTracks({ commit }, payload) {
    const response = await client.get(`/v1/reciters/${payload.reciter}/albums/${payload.album}/tracks`);
    commit('FETCH_TRACKS', {
      data: response.data.data,
    });
  },
  async fetchTrack({ commit }, payload) {
    const response = await client.get(
      `/v1/reciters/${payload.reciter}/albums/${payload.album}/tracks/${payload.track}?include=reciter,album,lyrics`,
    );
    commit('FETCH_TRACK', {
      data: response.data,
    });
    store.commit('reciters/FETCH_RECITER', {
      data: response.data.reciter,
    });
    store.commit('albums/FETCH_ALBUM', {
      data: response.data.album,
    });
  },
  async storeTrack({ commit }, payload) {
    const response = await client.post(
      `/v1/reciters/${payload.reciter}/albums/${payload.album}/tracks`,
      payload.form,
    );
    commit('STORE_TRACK', {
      data: response.data,
    });
  },
  async updateTrack({ commit }, payload) {
    const response = await client.post(
      `/v1/reciters/${payload.reciter}/albums/${payload.album}/tracks/${payload.track}`,
      payload.form,
    );
    commit('UPDATE_TRACK', {
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

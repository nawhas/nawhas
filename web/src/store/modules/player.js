const state = {
  track: null,
  queue: [],
};

const getters = {};

const mutations = {
  PLAY_TRACK(state, { track }) {
    state.track = track;
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

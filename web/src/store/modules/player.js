const state = {
  track: null,
  queue: [],
};

const getters = {};

const mutations = {
  PLAY_TRACK(state, { track }) {
    state.track = track;
  },
  ADD_TO_QUEUE(state, { track }) {
    state.queue.push(track);
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

export type CurrentTrackRef = number|null;
export type TrackQueue = Array<object>;

export interface PlayerState {
  current: CurrentTrackRef;
  queue: TrackQueue;
}

const state: PlayerState = {
  current: null,
  queue: [],
};

const getters = {
  track: (state: PlayerState) => (
    state.current === null
      ? null
      : state.queue[state.current]
  ),
  hasNext: (state: PlayerState) => (state.current !== null && state.queue.length > state.current + 1),
};

const mutations = {
  PLAY_TRACK(state: PlayerState, { track }) {
    state.queue = [track];
    state.current = 0;
  },
  ADD_TO_QUEUE(state: PlayerState, { track }) {
    state.queue.push(track);
  },
  NEXT(state: PlayerState) {
    if (state.current === null) {
      return;
    }
    state.current++;
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

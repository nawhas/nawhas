export type CurrentTrackRef = number|null;
export type TrackQueue = Array<QueuedTrack>;
export interface QueuedTrack {
  track: object;
  id: string;
}

function generateId(): string {
  return Math.random().toString(36).substr(2, 9);
}

export interface PlayerState {
  current: CurrentTrackRef;
  queue: TrackQueue;
}

const state: PlayerState = {
  current: null,
  queue: [],
};

const getters = {
  track: (state: PlayerState): QueuedTrack|null => (
    state.current === null
      ? null
      : (state.queue[state.current] || null)
  ),
  hasNext: (state: PlayerState) => (state.current !== null && state.queue.length > state.current + 1),
  hasPrevious: (state: PlayerState) => (state.current !== null && state.current !== 0),
};

const mutations = {
  PLAY_TRACK(state: PlayerState, { track }) {
    state.queue = [{
      track,
      id: generateId(),
    }];
    state.current = 0;
  },
  ADD_TO_QUEUE(state: PlayerState, { track }) {
    state.queue.push({
      track,
      id: generateId(),
    });
  },
  NEXT(state: PlayerState) {
    if (state.current === null) {
      return;
    }
    state.current++;
  },
  PREVIOUS(state: PlayerState) {
    if (state.current === null) {
      return;
    }
    state.current--;
  },
  REMOVE_TRACK(state: PlayerState, { id }) {
    const index = state.queue.findIndex((queued: QueuedTrack) => queued.id === id);
    state.queue.splice(index, 1);
    if (state.current !== null && state.current > index) {
      state.current--;
    }
  },
  SKIP_TO_TRACK(state: PlayerState, { id }) {
    const index = state.queue.findIndex((queued: QueuedTrack) => queued.id === id);
    state.current = index;
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

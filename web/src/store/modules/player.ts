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
  seek: number;
  duration: number;
}

const state: PlayerState = {
  current: null,
  seek: 0,
  duration: 0,
  queue: [],
};

const getters = {
  progress: (state: PlayerState): number => {
    if (!state.seek || !state.duration) {
      return 0;
    }
    return (state.seek / state.duration) * 100;
  },
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
  REMOVE_TRACK(state: PlayerState, payload) {
    let queueIndex;
    if (payload.id) {
      const { id } = payload;
      const index = state.queue.findIndex((queued: QueuedTrack) => queued.id === id);
      queueIndex = index;
    }
    if (payload.track) {
      const { track } = payload;
      for (let index = 0; index < state.queue.length; index++) {
        const queue = state.queue[index];
        if (queue.track === track) {
          queueIndex = index;
        }
      }
    }
    state.queue.splice(queueIndex, 1);
    if (state.current !== null && state.current > queueIndex) {
      state.current--;
    }
  },
  SKIP_TO_TRACK(state: PlayerState, { id }) {
    const index = state.queue.findIndex((queued: QueuedTrack) => queued.id === id);
    state.current = index;
  },
  UPDATE_TRACK_PROGRESS(state: PlayerState, { seek, duration }) {
    state.seek = seek;
    state.duration = duration;
  },
  STOP(state: PlayerState) {
    state.current = null;
    state.queue = [];
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

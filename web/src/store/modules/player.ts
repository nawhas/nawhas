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
  shuffledQueue: TrackQueue;
  seek: number;
  duration: number;
}

const state: PlayerState = {
  current: null,
  seek: 0,
  duration: 0,
  queue: [],
  shuffledQueue: [],
};

const getters = {
  progress: (state: PlayerState): number => {
    if (!state.seek || !state.duration) {
      return 0;
    }
    return (state.seek / state.duration) * 100;
  },
  queue: (state: PlayerState, getters: any): TrackQueue => {
    if (getters.isShuffled) {
      return state.shuffledQueue;
    }
    return state.queue;
  },
  isShuffled: (state: PlayerState): boolean => {
    if (state.shuffledQueue.length) {
      return true;
    }
    return false;
  },
  track: (state: PlayerState, getters: any): QueuedTrack|null => (
    state.current === null
      ? null
      : (getters.queue[state.current] || null)
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
  REMOVE_TRACK(state: PlayerState, getters: any, { id }) {
    const index = state.queue.findIndex((queued: QueuedTrack) => queued.id === id);
    state.queue.splice(index, 1);
    if (getters.isShuffled) {
      const shuffledIndex = state.shuffledQueue.findIndex((queued: QueuedTrack) => queued.id === id);
      state.shuffledQueue.splice(shuffledIndex, 1);
      if (state.current !== null && state.current > shuffledIndex) {
        state.current--;
      }
    }
    if (!getters.isShuffled) {
      if (state.current !== null && state.current > index) {
        state.current--;
      }
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
    state.shuffledQueue = [];
  },
  TOGGLE_SHUFFLE(state: PlayerState, getters: any) {
    if (getters.isShuffled) {
      const originalQueue = state.queue;
      const shuffledQueue: TrackQueue = [];
      while (originalQueue.length !== 0) {
        const randomIndex = Math.floor(Math.random() * originalQueue.length);
        shuffledQueue.push(originalQueue[randomIndex]);
        originalQueue.splice(randomIndex, 1);
      }

      const index = state.queue.indexOf(getters.track);
      if (index > 0) {
        shuffledQueue.splice(index, 1);
        shuffledQueue.unshift(state.queue[index]);
      }

      state.shuffledQueue = shuffledQueue;
    } else {
      state.shuffledQueue = [];
    }
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

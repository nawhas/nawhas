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
  isShuffled: boolean;
}

const state: PlayerState = {
  current: null,
  seek: 0,
  duration: 0,
  queue: [],
  shuffledQueue: [],
  isShuffled: false,
};

const getters = {
  progress: (state: PlayerState): number => {
    if (!state.seek || !state.duration) {
      return 0;
    }
    return (state.seek / state.duration) * 100;
  },
  queue: (state: PlayerState): TrackQueue => {
    if (state.isShuffled) {
      return state.shuffledQueue;
    }
    return state.queue;
  },
  isShuffled: (state: PlayerState): boolean => state.isShuffled,
  track: (state: PlayerState, getters: any): QueuedTrack|null => (
    state.current === null
      ? null
      : (getters.queue[state.current] || null)
  ),
  hasNext: (state: PlayerState, getters: any) => (state.current !== null && getters.queue.length > state.current + 1),
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
    if (state.isShuffled) {
      const shuffledIndex = state.shuffledQueue.findIndex((queued: QueuedTrack) => queued.id === id);
      state.shuffledQueue.splice(shuffledIndex, 1);
      if (state.current !== null && state.current > shuffledIndex) {
        state.current--;
      }
    }
    if (!state.isShuffled) {
      if (state.current !== null && state.current > index) {
        state.current--;
      }
    }
  },
  SKIP_TO_TRACK(state: PlayerState, { id }) {
    if (state.isShuffled) {
      const index = state.shuffledQueue.findIndex((queued: QueuedTrack) => queued.id === id);
      state.current = index;
    } else {
      const index = state.queue.findIndex((queued: QueuedTrack) => queued.id === id);
      state.current = index;
    }
  },
  UPDATE_TRACK_PROGRESS(state: PlayerState, { seek, duration }) {
    state.seek = seek;
    state.duration = duration;
  },
  STOP(state: PlayerState) {
    state.current = null;
    state.queue = [];
    state.shuffledQueue = [];
    state.isShuffled = false;
  },
  TOGGLE_SHUFFLE(state: PlayerState) {
    state.isShuffled = !state.isShuffled;
    if (state.isShuffled) {
      const originalIndex: number = state.current;
      let shuffledIndex: number|null = null;
      const shuffledQueue: TrackQueue = [];
      // eslint-disable-next-line guard-for-in
      for (const i in state.queue) {
        let randomIndex = Math.floor(Math.random() * state.queue.length);
        while (shuffledQueue.includes(state.queue[randomIndex])) {
          randomIndex = Math.floor(Math.random() * state.queue.length);
        }
        shuffledQueue[i] = state.queue[randomIndex];
      }

      shuffledIndex = shuffledQueue.findIndex((queued: QueuedTrack) => queued.id === state.queue[originalIndex].id);
      if (shuffledIndex > 0) {
        shuffledQueue.splice(shuffledIndex, 1);
        shuffledQueue.unshift(state.queue[originalIndex]);
      }

      state.current = 0;
      state.shuffledQueue = shuffledQueue;
    } else {
      console.log('Here is the original queue');
      console.log(state.queue);
      console.log('Here is the shuffled queue');
      console.log(state.shuffledQueue);
      console.log(`The current track that is playing from the shuffled queue is ID: ${state.current}`);
      const index = state.queue.findIndex((queued: QueuedTrack) => queued.id === state.shuffledQueue[state.current].id);
      state.current = index;
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

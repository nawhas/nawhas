export type CurrentTrackRef = number|null;
export type TrackQueue = Array<QueuedTrack>;
export type RepeatType = null|'one'|'all';
export interface QueuedTrack {
  track: object;
  id: string;
}

function generateId(): string {
  return Math.random().toString(36).substr(2, 9);
}

/**
 * Shuffle an array using the Fisher-Yates Shuffle algorithm
 */
function shuffle(arr: Array<any>): Array<any> {
  const shuffled = [...arr];

  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }

  return shuffled;
}

function getTrackIndexById(state: PlayerState, id: string) {
  if (state.isShuffled) {
    return state.shuffled.findIndex((queued: QueuedTrack) => queued.id === id);
  }

  return state.queue.findIndex((queued: QueuedTrack) => queued.id === id);
}

export interface PlayerState {
  current: CurrentTrackRef;
  queue: TrackQueue;
  shuffled: TrackQueue;
  seek: number;
  duration: number;
  isShuffled: boolean;
  repeat: RepeatType;
}

const state: PlayerState = {
  current: null,
  seek: 0,
  duration: 0,
  queue: [],
  shuffled: [],
  isShuffled: false,
  repeat: null,
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
      return state.shuffled;
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
    if (state.queue.length === 0) {
      state.current = 0;
    }
    state.queue.push({
      track,
      id: generateId(),
    });
  },
  PLAY_ALBUM(state: PlayerState, { tracks, start }) {
    const queue: TrackQueue = [];
    tracks.map((track) => queue.push({ track, id: generateId() }));

    const current = start ? queue.findIndex((queued: QueuedTrack) => start.id === (queued.track as any).id) : 0;
    state.queue = queue;
    state.current = current;
  },
  ADD_ALBUM_TO_QUEUE(state: PlayerState, { tracks }) {
    tracks.map((track) => state.queue.push({
      track,
      id: generateId(),
    }));
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
      const shuffledIndex = state.shuffled.findIndex((queued: QueuedTrack) => queued.id === id);
      state.shuffled.splice(shuffledIndex, 1);
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
    const index = getTrackIndexById(state, id);
    state.current = index;
  },
  UPDATE_TRACK_PROGRESS(state: PlayerState, { seek, duration }) {
    state.seek = seek;
    state.duration = duration;
  },
  STOP(state: PlayerState) {
    state.current = null;
    state.queue = [];
    state.shuffled = [];
    state.isShuffled = false;
  },
  TOGGLE_SHUFFLE(state: PlayerState) {
    const payload = {
      isShuffled: !state.isShuffled,
      current: state.current,
      shuffled: state.shuffled,
    };

    if (payload.isShuffled) {
      const tracks = [...state.queue];

      // Don't include the current track in the list to be shuffled.
      if (state.current !== null) {
        tracks.splice(state.current, 1);
      }

      const shuffled = shuffle(tracks);

      if (state.current !== null) {
        shuffled.unshift(state.queue[state.current]);
      }

      payload.current = 0;
      payload.shuffled = shuffled;
    } else {
      if (state.current !== null) {
        const index = state.queue.findIndex(
          (queued: QueuedTrack) => queued.id === state.shuffled[(state.current as number)].id,
        );
        payload.current = index;
      }
      payload.shuffled = [];
    }

    state.current = payload.current;
    state.shuffled = payload.shuffled;
    state.isShuffled = payload.isShuffled;
  },
  TOGGLE_REPEAT(state: PlayerState) {
    let repeat: RepeatType = null;
    if (state.repeat === null) {
      repeat = 'all';
    }
    if (state.repeat === 'all') {
      repeat = 'one';
    }
    if (state.repeat === 'one') {
      repeat = null;
    }
    state.repeat = repeat;
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

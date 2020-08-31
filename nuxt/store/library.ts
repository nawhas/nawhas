import { ActionTree, MutationTree, GetterTree } from 'vuex';
import { RootState } from '@/store/index';
import { TracksPayload } from '@/api/library';
import { EventBus } from '@/events';
import { TOAST_SHOW, ToastOptions } from '@/events/toaster';

export interface LibraryState {
  trackIds: Array<string>;
  initialized: boolean;
}

const state = (): LibraryState => ({
  trackIds: [],
  initialized: false,
});

const mutations: MutationTree<LibraryState> = {
  INITIALIZE(state) {
    state.initialized = true;
  },
  SET_TRACK_IDS(state, trackIds) {
    state.trackIds = trackIds;
  },
  TRACKS_ADDED(state, ids: Array<string>) {
    state.trackIds.push(...ids);
  },
  TRACK_REMOVED(state, ids: Array<string>) {
    state.trackIds = state.trackIds.filter((i) => !ids.includes(i));
  },
};

const actions: ActionTree<LibraryState, RootState> = {
  async initialize({ commit, state }) {
    if (state.initialized) {
      return;
    }

    try {
      const response = await this.$api.library.trackIds();
      commit('SET_TRACK_IDS', response);
    } catch {} finally {
      commit('INITIALIZE');
    }
  },
  async getTrackIds({ commit }) {
    try {
      const response = await this.$api.library.trackIds();
      commit('SET_TRACK_IDS', response);
    } catch {
      commit('SET_TRACK_IDS', []);
    }
  },

  async saveTrack({ commit, rootState }, payload: TracksPayload) {
    if (rootState.auth.user) {
      commit('TRACKS_ADDED', payload.ids);
      await this.$api.library.saveTrack(payload);
      EventBus.$emit(TOAST_SHOW, {
        text: 'Added to Library',
        type: 'success',
        icon: 'check',
      } as ToastOptions);
      this.dispatch('library/getTrackIds');
    } else {
      commit('auth/PROMPT_USER', { prompt: 'favourite' }, { root: true });
    }
  },

  async removeTrack({ commit, rootState }, payload: TracksPayload) {
    if (!rootState.auth.user) {
      return;
    }

    commit('TRACK_REMOVED', payload.ids);

    await this.$api.library.removeTrack(payload);

    EventBus.$emit(TOAST_SHOW, {
      text: 'Removed from Library',
    });
    this.dispatch('library/getTrackIds');
  },
};

const getters: GetterTree<LibraryState, RootState> = {
  isTrackSaved: (state) => (trackId) => state.trackIds.includes(trackId),
};

export default {
  state,
  actions,
  mutations,
  getters,
};

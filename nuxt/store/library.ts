import { ActionTree, MutationTree, GetterTree } from 'vuex';
import { RootState } from '@/store/index';
import { TracksPayload } from '@/api/library';

export interface LibraryState {
  trackIds: Array<string>;
}

const state = (): LibraryState => ({
  trackIds: [],
});

const mutations: MutationTree<LibraryState> = {
  SET_TRACK_IDS(state, trackIds) {
    state.trackIds = trackIds;
  },
};

const actions: ActionTree<LibraryState, RootState> = {
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
      await this.$api.library.saveTrack(payload);
      this.dispatch('library/getTrackIds');
    } else {
      commit('auth/PROMPT_USER', { prompt: 'favourite' }, { root: true });
    }
  },

  async removeTrack(_, payload: TracksPayload) {
    await this.$api.library.removeTrack(payload);
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

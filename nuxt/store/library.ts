import { ActionTree, MutationTree, GetterTree } from 'vuex';
import { RootState } from '@/store/index';
import { TracksPayload } from '@/api/library';

interface libraryState {
  tracks: Array<any>;
  trackIds: Array<string>;
}

const state = (): libraryState => ({
  tracks: [],
  trackIds: [],
});

const mutations: MutationTree<libraryState> = {
  SET_TRACKS(state, tracks) {
    state.tracks = tracks;
  },
  SET_TRACK_IDS(state, trackIds) {
    state.trackIds = trackIds;
  },
};

const actions: ActionTree<libraryState, RootState> = {
  async getTracks({ commit }) {
    const response = await this.$api.library.tracks();
    commit('SET_TRACKS', response.data);
  },

  async getTrackIds({ commit }) {
    try {
      const response = await this.$api.library.trackIds();
      commit('SET_TRACK_IDS', response);
    } catch {}
  },

  async saveTrack(context, payload: TracksPayload) {
    // @ts-ignore
    if (context.rootState.auth.user) {
      await this.$api.library.saveTrack(payload);
      this.dispatch('library/getTrackIds');
    } else {
      context.commit('auth/PROMPT_USER', { prompt: 'favourite' }, { root: true });
    }
  },

  async removeTrack(_, payload: TracksPayload) {
    await this.$api.library.removeTrack(payload);
    this.dispatch('library/getTrackIds');
  },
};

const getters: GetterTree<libraryState, RootState> = {
  isTrackSaved: (state) => (trackId) => state.trackIds.includes(trackId),
};

export default {
  state,
  actions,
  mutations,
  getters,
};

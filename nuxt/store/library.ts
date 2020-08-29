import { ActionTree, MutationTree } from 'vuex';
import { RootState } from '@/store/index';

interface libraryState {
  tracks: Array<any>;
}

const state = (): libraryState => ({
  tracks: [],
});

const mutations: MutationTree<libraryState> = {
  SET_TRACKS(state, data) {
    state.tracks = data;
  },
};

const actions: ActionTree<libraryState, RootState> = {
  async getTracks({ commit }) {
    const response = await this.$api.library.tracks();
    commit('SET_TRACKS', response.data);
  },

  async saveTrack(context, payload) {
    // @ts-ignore
    if (context.rootState.auth.user) {
      await this.$api.library.saveTrack(payload);
      this.dispatch('library/getTracks');
    } else {
      // if not then commit a mutation to auth state creating a prompt
      console.log('user is not logged in');
    }
  },
};

export default {
  state,
  actions,
  mutations,
};

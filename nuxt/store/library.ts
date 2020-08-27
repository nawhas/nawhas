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
    const response = await this.$api.library.tracks;
    commit('SET_TRACKS', response);
  },
};

export default {
  state,
  actions,
  mutations,
};

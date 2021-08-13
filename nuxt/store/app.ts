import { MutationTree } from 'vuex';

export interface AppState {
  version: string|null;
}

const state = (): AppState => ({
  version: null,
});

const mutations: MutationTree<AppState> = {
  SET_VERSION(state: AppState, version: string) {
    state.version = version;
  },
};

export default {
  state,
  mutations,
  namespaced: true,
};

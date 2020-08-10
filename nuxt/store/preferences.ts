import { MutationTree } from 'vuex';
import { Theme } from '@/entities/theme';

interface PreferencesState {
  theme: Theme;
}

const state = (): PreferencesState => ({
  theme: 'auto',
});

const mutations: MutationTree<PreferencesState> = {
  SET_THEME(state: PreferencesState, theme: Theme) {
    state.theme = theme;
  },
};

export default {
  state,
  mutations,
  namespaced: true,
};

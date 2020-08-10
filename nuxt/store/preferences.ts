import { ActionTree, MutationTree } from 'vuex';
import { RootState } from '@/store';

export type Theme = 'light'|'dark'|'auto';

interface PreferencesState {
  theme: Theme;
}
const THEME_STORAGE_KEY = 'preferences.theme';

const state = (): PreferencesState => ({
  theme: 'auto',
});

const mutations: MutationTree<PreferencesState> = {
  SET_THEME(state: PreferencesState, theme: Theme) {
    state.theme = theme;
    localStorage.setItem(THEME_STORAGE_KEY, theme);
  },
};

const actions: ActionTree<PreferencesState, RootState> = {
  initialize({ commit }) {
    const theme = localStorage.getItem(THEME_STORAGE_KEY) ?? 'auto';
    commit('SET_THEME', theme);
  },
};

export default {
  state,
  mutations,
  actions,
  namespaced: true,
};

type Theme = 'light'|'dark'|'auto';
export interface UserPreferences {
  theme: Theme;
}

const state: UserPreferences = {
  theme: (localStorage.getItem('preferences.theme') as Theme) || 'auto',
};

const getters = {};

const mutations = {
  SET_THEME(state: UserPreferences, theme: Theme) {
    state.theme = theme;
    localStorage.setItem('preferences.theme', state.theme);
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

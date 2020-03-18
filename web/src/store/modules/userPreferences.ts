export interface UserPreferences {
  theme: 'light'|'dark';
}

const state: UserPreferences = {
  theme: 'light',
};

const getters = {
  isDark: (state: UserPreferences): boolean => (state.theme === 'dark'),
};

const mutations = {
  SET_THEME(state: UserPreferences, { isDark }) {
    state.theme = (isDark) ? 'dark' : 'light';
    this.$vuetify.theme.dark = !!(isDark);
  },
};

export default {
  state,
  mutations,
  getters,
  namespaced: true,
};

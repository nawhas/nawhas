import { ActionContext, ActionTree, MutationTree, GetterTree } from 'vuex';
import { LoginPayload, RegisterPayload, Role, User } from '@/api/auth';

import { RootState } from '@/store';

interface AuthState {
  user: User | null;
  initialized: boolean;
}
type Context = ActionContext<AuthState, RootState>;

const state = (): AuthState => ({
  user: null,
  initialized: false,
});

const mutations: MutationTree<AuthState> = {
  INITIALIZE(state, user: User | null) {
    state.user = user;
    state.initialized = true;
  },

  LOGIN(state, user: User) {
    state.user = user;
  },

  LOGOUT(state) {
    state.user = null;
  },
};

const actions: ActionTree<AuthState, RootState> = {
  async login({ commit }, payload: LoginPayload) {
    const user = await this.$api.auth.login(payload);

    commit('LOGIN', user);
  },
  async register({ commit }, payload: RegisterPayload) {
    const user = await this.$api.auth.register(payload);

    commit('LOGIN', user);
  },
  async logout({ commit }) {
    commit('LOGOUT');
    await this.$api.auth.logout();
  },
  async check({ commit }: Context) {
    try {
      const user = await this.$api.auth.user();
      commit('INITIALIZE', user);
    } catch (e) {
      // User not logged in.
      commit('INITIALIZE', null);
    }
  },
};

const getters: GetterTree<AuthState, RootState> = {
  user: (state) => state.user,
  authenticated: (state) => state.user !== null,
  isModerator: (state) => state.user?.role === Role.Moderator,
  role: (state) => state.user?.role ?? Role.Guest,
};

export default {
  state,
  actions,
  mutations,
  getters,
};

import { ActionContext, ActionTree, MutationTree } from 'vuex';
import { LoginPayload, RegisterPayload, User } from '@/api/auth';

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

export default {
  state,
  actions,
  mutations,
};

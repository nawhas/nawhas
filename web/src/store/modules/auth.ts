import client from '@/services/client';

export interface AuthState {
  user: any;
  initialized: boolean;
}

const state: AuthState = {
  user: null,
  initialized: false,
};

const mutations = {
  INITIALIZE(state: AuthState, { user }) {
    state.user = user;
    state.initialized = true;
  },
  LOGIN(state: AuthState, { user }) {
    state.user = user;
  },
};

const actions = {
  async login({ commit }, { email, password }) {
    await client.get('/airlock/csrf-cookie');
    const response = await client.post('/v1/auth/login', { email, password });

    commit('LOGIN', { user: response.data });
  },
  async check({ commit }) {
    await client.get('/airlock/csrf-cookie');
    try {
      const response = await client.get('/v1/auth/user');
      commit('INITIALIZE', { user: response.data });
    } catch (e) {
      // User not logged in.
      commit('INITIALIZE', { user: null });
    }
  },
};

const getters = {
  authenticated(state: AuthState): boolean {
    return state.user !== null;
  },
  role(state: AuthState): string {
    return state.user ? state.user.role : null;
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
  namespaced: true,
};

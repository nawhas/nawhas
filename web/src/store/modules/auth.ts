import client from '@/services/client';

export const Role = {
  MODERATOR: 'moderator',
  CONTRIBUTOR: 'contributor',
  GUEST: 'guest',
};

export type RoleValue = 'moderator' | 'contributor' | 'guest';

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
  LOGOUT(state: AuthState) {
    state.user = null;
  },
};

const actions = {
  async login({ commit }, { email, password }) {
    const response = await client.post('/v1/auth/login', { email, password });

    commit('LOGIN', { user: response.data });
  },
  async logout({ commit }) {
    commit('LOGOUT');
    client.post('/v1/auth/logout');
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
  role(state: AuthState): RoleValue {
    return state.user ? state.user.role : Role.GUEST;
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
  namespaced: true,
};

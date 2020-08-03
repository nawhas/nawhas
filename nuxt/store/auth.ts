import client from '@/services/client';
import { User, Data as UserData, Role } from '@/entities/user';
import { ActionContext } from 'vuex';

/*
|--------------------------------------------------------------------------
| Interfaces & Types
|--------------------------------------------------------------------------
*/
export interface AuthState {
  user: UserData | null;
  initialized: boolean;
}

export interface LoginActionPayload {
  email: string;
  password: string;
}

export interface RegisterActionPayload {
  name: string;
  email: string;
  password: string;
  nickname: string | null;
}

export interface UserPayload {
  user: UserData | null;
}

type Context = ActionContext<AuthState, any>;

/*
|--------------------------------------------------------------------------
| Store State, Mutations, Actions, & Getters
|--------------------------------------------------------------------------
*/
const state = () : AuthState => ({
  user: null,
  initialized: false,
});

const mutations = {
  INITIALIZE(state: AuthState, { user }: UserPayload) {
    state.user = user;
    state.initialized = true;
  },
  LOGIN(state: AuthState, { user }: UserPayload) {
    state.user = user;
  },
  LOGOUT(state: AuthState) {
    state.user = null;
  },
  REGISTER(state: AuthState, { user }: UserPayload) {
    state.user = user;
  },
};

const actions = {
  async login({ commit }: Context, { email, password }: LoginActionPayload) {
    const response = await client.post('/v1/auth/login', { email, password });

    commit('LOGIN', { user: response.data });
  },
  async register({ commit }: Context, {
    name,
    email,
    password,
    nickname,
  }: RegisterActionPayload) {
    const response = await client.post('/v1/auth/register', {
      name,
      email,
      password,
      nickname,
    });

    commit('REGISTER', { user: response.data });
  },
  async logout({ commit }: Context) {
    commit('LOGOUT');
    await client.post('/v1/auth/logout');
  },
  async check({ commit }: Context) {
    await client.get('/sanctum/csrf-cookie');
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
  user(state: AuthState): User | null {
    return state.user ? new User(state.user) : null;
  },
  authenticated(state: AuthState): boolean {
    return state.user !== null;
  },
  role(state: AuthState): Role {
    return state.user ? state.user.role : Role.Guest;
  },
  isModerator(state: AuthState): boolean {
    return !!(state.user && state.user.role === Role.Moderator);
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
};

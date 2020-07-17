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

export enum Mutations {
  Initialize = '[Auth] Initialize auth state',
  Login = '[Auth] User logged in',
  Logout = '[Auth] User logged out',
  Register = '[Auth] User registered',
}

export enum Actions {
  Login = 'auth.login',
  Logout = 'auth.logout',
  Check = 'auth.check',
  Register = 'auth.register',
}

export enum Getters {
  User = 'auth.user',
  Authenticated = 'auth.authenticated',
  Role = 'auth.role',
  IsModerator = 'auth.isModerator',
}

/*
|--------------------------------------------------------------------------
| Store State, Mutations, Actions, & Getters
|--------------------------------------------------------------------------
*/
const state: AuthState = {
  user: null,
  initialized: false,
};

const mutations = {
  [Mutations.Initialize](state: AuthState, { user }: UserPayload) {
    state.user = user;
    state.initialized = true;
  },
  [Mutations.Login](state: AuthState, { user }: UserPayload) {
    state.user = user;
  },
  [Mutations.Logout](state: AuthState) {
    state.user = null;
  },
  [Mutations.Register](state: AuthState, { user }: UserPayload) {
    state.user = user;
  },
};


const actions = {
  async [Actions.Login]({ commit }: Context, { email, password }: LoginActionPayload) {
    const response = await client.post('/v1/auth/login', { email, password });

    commit(Mutations.Login, { user: response.data });
  },
  async [Actions.Register]({ commit }: Context, {
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

    commit(Mutations.Register, { user: response.data });
  },
  async [Actions.Logout]({ commit }: Context) {
    commit(Mutations.Logout);
    client.post('/v1/auth/logout');
  },
  async [Actions.Check]({ commit }: Context) {
    await client.get('/sanctum/csrf-cookie');
    try {
      const response = await client.get('/v1/auth/user');
      commit(Mutations.Initialize, { user: response.data });
    } catch (e) {
      // User not logged in.
      commit(Mutations.Initialize, { user: null });
    }
  },
};

const getters = {
  [Getters.User](state: AuthState): User | null {
    return state.user ? new User(state.user) : null;
  },
  [Getters.Authenticated](state: AuthState): boolean {
    return state.user !== null;
  },
  [Getters.Role](state: AuthState): Role {
    return state.user ? state.user.role : Role.Guest;
  },
  [Getters.IsModerator](state: AuthState): boolean {
    return !!(state.user && state.user.role === Role.Moderator);
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
};

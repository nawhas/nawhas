import { ActionContext, ActionTree, MutationTree, GetterTree } from 'vuex';
import { LoginPayload, RegisterPayload } from '@/api/auth';
import { User, Role } from '@/entities/user';
import { AuthPrompt } from '@/entities/auth';
import { RootState } from '@/store';

export interface AuthState {
  user: User | null;
  initialized: boolean;
  prompt: AuthPrompt | null;
}

type Context = ActionContext<AuthState, RootState>;

const state = (): AuthState => ({
  user: null,
  initialized: false,
  prompt: null,
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

  PROMPT_USER(state, prompt: Partial<AuthPrompt>) {
    state.prompt = {
      type: 'register',
      ...prompt,
    };
  },

  REMOVE_PROMPT(state) {
    state.prompt = null;
  },
};

const actions: ActionTree<AuthState, RootState> = {
  async login({ commit, dispatch }, payload: LoginPayload) {
    const user = await this.$api.auth.login(payload);

    commit('LOGIN', user);

    dispatch('features/fetch', null, { root: true });
    dispatch('library/getTrackIds', null, { root: true });
  },
  async register({ commit, dispatch }, payload: RegisterPayload) {
    const user = await this.$api.auth.register(payload);

    commit('LOGIN', user);

    dispatch('features/fetch', null, { root: true });
    dispatch('library/getTrackIds', null, { root: true });
  },
  async logout({ commit, dispatch }) {
    commit('LOGOUT');
    await this.$api.auth.logout();
    dispatch('library/getTrackIds', null, { root: true });
  },
  async initialize({ state, dispatch }: Context) {
    if (state.initialized) {
      return;
    }
    await dispatch('reinitialize');
  },
  async reinitialize({ commit }: Context) {
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
  showLoginDialog: (state) => state.prompt?.type === 'login',
  showRegisterDialog: (state) => state.prompt?.type === 'register',
  showPasswordResetRequestDialog: (state) => state.prompt?.type === 'reset.request',
};

export default {
  state,
  actions,
  mutations,
  getters,
};

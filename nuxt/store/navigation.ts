import { ActionContext, ActionTree, MutationTree, GetterTree } from 'vuex';
import { RootState } from '@/store/index';

/*
|--------------------------------------------------------------------------
| Interfaces & Types
|--------------------------------------------------------------------------
*/
interface NavigationState {
  history: Array<string>;
}
type Context = ActionContext<NavigationState, any>;
/*
|--------------------------------------------------------------------------
| Store State, Mutations, Actions, & Getters
|--------------------------------------------------------------------------
*/
const state = (): NavigationState => ({
  history: [],
});

const getters: GetterTree<NavigationState, RootState> = {

};

const mutations: MutationTree<NavigationState> = {
  HISTORY_PUSH(state, path: string) {
    state.history.push(path);
  },
  HISTORY_POP(state) {
    state.history.pop();
  },
};

const actions: ActionTree<NavigationState, RootState> = {
  // async back({ commit }: Context) {
  // commit('INITIALIZE', features);
  // },
};

export default {
  state,
  mutations,
  getters,
  actions,
};

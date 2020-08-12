import { ActionContext, ActionTree, MutationTree, GetterTree } from 'vuex';
import { Feature, FeatureMap } from '@/entities/feature';
import { RootState } from '@/store/index';

/*
|--------------------------------------------------------------------------
| Interfaces & Types
|--------------------------------------------------------------------------
*/
interface FeaturesState {
  features: FeatureMap;
  initialized: boolean;
}
type Context = ActionContext<FeaturesState, any>;
/*
|--------------------------------------------------------------------------
| Store State, Mutations, Actions, & Getters
|--------------------------------------------------------------------------
*/
const state = (): FeaturesState => ({
  features: {},
  initialized: false,
});

const getters: GetterTree<FeaturesState, RootState> = {
  enabled: (state) => (feature: Feature) => state.features[feature] ?? false,
};

const mutations: MutationTree<FeaturesState> = {
  INITIALIZE(state, features: FeatureMap) {
    state.features = features;
    state.initialized = true;
  },
};

const actions: ActionTree<FeaturesState, RootState> = {
  async fetch({ commit }: Context) {
    const features = await this.$api.features.index();
    commit('INITIALIZE', features);
  },
};

export default {
  state,
  mutations,
  getters,
  actions,
};

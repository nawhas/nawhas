import client from '@/services/client';
import { Features } from '@/entities/features';
import { ActionContext } from 'vuex';

/*
|--------------------------------------------------------------------------
| Interfaces & Types
|--------------------------------------------------------------------------
*/
export interface FeaturesState {
  features: Features;
  initialized: boolean;
}

type Context = ActionContext<FeaturesState, any>;

interface FeaturesPayload {
  features: Features;
}
/*
|--------------------------------------------------------------------------
| Store State, Mutations, Actions, & Getters
|--------------------------------------------------------------------------
*/
const state = (): FeaturesState => ({
  features: {},
  initialized: false,
});

const getters = {
  enabled: (state: FeaturesState) => (feature: string) => !!state.features[feature],
};

const mutations = {
  INITIALIZE(state: FeaturesState, { features }: FeaturesPayload) {
    state.features = features;
    state.initialized = true;
  },
};

const actions = {
  async fetch({ commit }: Context) {
    const response = await client.get('/v1/features');

    const payload: FeaturesPayload = {
      features: response.data.data,
    };

    commit('INITIALIZE', payload);
  },
};

export default {
  state,
  mutations,
  getters,
  actions,
};

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

export enum Mutations {
  Initialize = '[Features] Initialize features state',
}

export enum Actions {
  Fetch = 'features.fetch',
}

interface FeaturesPayload {
  features: Features;
}
/*
|--------------------------------------------------------------------------
| Store State, Mutations, Actions, & Getters
|--------------------------------------------------------------------------
*/
const state: FeaturesState = {
  features: {},
  initialized: false,
};

const getters = {};

const mutations = {
  [Mutations.Initialize](state: FeaturesState, { features }: FeaturesPayload) {
    state.features = features;
    state.initialized = true;
  },
};

const actions = {
  async [Actions.Fetch]({ commit }: Context) {
    const response = await client.get('/v1/features');

    const payload: FeaturesPayload = {
      features: response.data,
    };

    commit(Mutations.Initialize, payload);
  },
};

export default {
  state,
  mutations,
  getters,
  actions,
};

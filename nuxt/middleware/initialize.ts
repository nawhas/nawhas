import { Middleware } from '@nuxt/types';

const initialize: Middleware = ({ store }) => {
  return store.dispatch('auth/check');
};

export default initialize;

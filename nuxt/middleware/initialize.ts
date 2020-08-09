import { Middleware } from '@nuxt/types';

const initialize: Middleware = async ({ store }) => {
  await Promise.all([
    store.dispatch('auth/check'),
    store.dispatch('features/fetch'),
  ]);
};

export default initialize;

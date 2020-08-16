import { Middleware } from '@nuxt/types';

const initialize: Middleware = async ({ store, $config }) => {
  await Promise.all([
    store.dispatch('auth/check'),
    store.dispatch('features/fetch'),
  ]);

  // eslint-disable-next-line no-console
  console.log('App Release: ', $config.release);
};

export default initialize;

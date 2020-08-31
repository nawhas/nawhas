import { Middleware } from '@nuxt/types';

const initialize: Middleware = async ({ store, $config }) => {
  // eslint-disable-next-line no-console
  console.log('App Release: ', $config.release);
  await store.dispatch('auth/initialize').catch(() => null);
};

export default initialize;

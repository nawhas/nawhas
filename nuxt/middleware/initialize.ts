import { Middleware } from '@nuxt/types';

const initialize: Middleware = async ({ store, $config }) => {
  // eslint-disable-next-line no-console
  console.log('App Release: ', $config.release);

  store.commit('app/SET_VERSION', $config.release);

  await Promise.all([
    store.dispatch('auth/initialize').catch(() => null),
    store.dispatch('library/initialize').catch(() => null),
  ]);
};

export default initialize;

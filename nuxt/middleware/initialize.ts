import { Middleware } from '@nuxt/types';

const initialize: Middleware = ({ $config }) => {
  // eslint-disable-next-line no-console
  console.log('App Release: ', $config.release);
};

export default initialize;

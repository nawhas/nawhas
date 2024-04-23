import { Plugin } from '@nuxt/types';

export interface InjectedErrorsPlugin {
  handle404: () => Promise<void>;
  handle423: () => void;
}

const ErrorsPlugin: Plugin = ({ error, $axios, route, redirect, res }, inject) => {
  const errors: InjectedErrorsPlugin = {
    handle404: async () => {
      try {
        const response = await $axios.$get('v1/redirect', {
          params: route.params,
        });
        res.setHeader('Cache-Control', 'no-store');
        redirect(301, response.to);
      } catch (e) {
        error({ statusCode: 404, message: 'Page not found.' });
      }
    },
    handle423: () => {
      error({ statusCode: 423, message: 'Resource is currently locked.' });
    },
  };

  inject('errors', errors);
};

export default ErrorsPlugin;

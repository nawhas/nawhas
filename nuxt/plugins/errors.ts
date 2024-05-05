import { Plugin } from '@nuxt/types';

export interface InjectedErrorsPlugin {
  handle404: () => Promise<void>;
  handle423: (draftRecord: string) => void;
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
    handle423: (draftRecord: string) => {
      error({ statusCode: 423, message: `Someone else is working on this ${draftRecord} right now! Try again in a few minutes.` });
    },
  };

  inject('errors', errors);
};

export default ErrorsPlugin;

import { Plugin } from '@nuxt/types';

export interface InjectedErrorsPlugin {
  handle404: () => Promise<void>;
}

const ErrorsPlugin: Plugin = ({ error }, inject) => {
  const errors: InjectedErrorsPlugin = {
    handle404: () => {
      console.log('Custom 404 handler');
      error({ statusCode: 404, message: 'Page not found.' });
      return Promise.resolve();
    },
  };

  inject('errors', errors);
};

export default ErrorsPlugin;

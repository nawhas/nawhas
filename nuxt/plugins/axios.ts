import https from 'https';
import createAuthRefreshInterceptor from 'axios-auth-refresh';
import { Plugin } from '@nuxt/types';

const AxiosPlugin: Plugin = ({ $axios, $config, req }) => {
  if ($config.ignoreSslErrors) {
    $axios.defaults.httpsAgent = new https.Agent({ rejectUnauthorized: false });
  }

  const onAxiosRequestFailure = async (failure) => {
    if (failure.response.status === 419) {
      await $axios.get('/sanctum/csrf-cookie');
    }

    return Promise.resolve();
  };

  createAuthRefreshInterceptor($axios, onAxiosRequestFailure, {
    statusCodes: [419],
  });

  $axios.onRequest((config) => {
    if (process.server) {
      config.headers = { ...config.headers, referer: req.headers.host };
    }
    return config;
  });
};

export default AxiosPlugin;

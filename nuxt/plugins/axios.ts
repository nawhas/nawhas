import https from 'https';
import createAuthRefreshInterceptor from 'axios-auth-refresh';

export default function ({ $axios, $config }) {
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
}

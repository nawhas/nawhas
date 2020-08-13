import https from 'https';

export default function ({ $axios, $config }) {
  if ($config.ignoreSslErrors) {
    $axios.defaults.httpsAgent = new https.Agent({ rejectUnauthorized: false });
  }
}

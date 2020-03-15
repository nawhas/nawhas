/* eslint-disable @typescript-eslint/no-var-requires,@typescript-eslint/camelcase */
const fs = require('fs');
const path = require('path');

const https = process.env.NODE_ENV === 'production' ? false : {
  key: fs.readFileSync(path.resolve(__dirname, '../docker/nginx/certs/nawhas.test.key')),
  cert: fs.readFileSync(path.resolve(__dirname, '../docker/nginx/certs/nawhas.test.crt')),
};

module.exports = {
  transpileDependencies: [
    'vuetify',
  ],
  productionSourceMap: false,
  devServer: {
    https,
    host: 'nawhas.test',
    proxy: {
      '/api': {
        target: 'https://api.nawhas.test',
        pathRewrite: { '^/api': '' },
      },
    },
    public: process.env.LIVE_SHARE ? 'https://localhost:8080' : undefined,
  },
  pwa: {
    name: 'Nawhas',
    shortName: 'Nawhas',
    themeColor: '#da0000',
    backgroundColor: '#ff7252',
    display: 'standalone',
    iconPaths: {
      favicon32: 'img/icons/favicon-32x32.png',
      favicon16: 'img/icons/favicon-16x16.png',
      appleTouchIcon: 'img/icons/apple-touch-icon.png',
      maskIcon: 'img/icons/safari-pinned-tab.svg',
      msTileImage: 'img/icons/icon-144x144.png',
    },
  },
};

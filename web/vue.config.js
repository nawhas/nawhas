/* eslint-disable @typescript-eslint/camelcase */
module.exports = {
  transpileDependencies: [
    'vuetify',
  ],
  productionSourceMap: false,
  devServer: {
    https: true,
  },
  pwa: {
    name: 'Nawhas.com',
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

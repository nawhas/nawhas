import fs from 'fs';
import path from 'path';
require('dotenv').config();

const TITLE_SUFFIX = 'Nawhas.com';
const DEFAULT_DESCRIPTION = 'Welcome to Nawhas.com, the most advanced library of nawhas online.';

const https = process.env.APP_ENV === 'development' ? {
  key: fs.readFileSync(path.resolve(__dirname, '../docker/nginx/certs/nawhas.test.key')),
  cert: fs.readFileSync(path.resolve(__dirname, '../docker/nginx/certs/nawhas.test.crt')),
} : false;

const proxy = process.env.PROXY_API ? {
  // For local dev only
  '/api': {
    target: 'https://api.nawhas.test/',
    pathRewrite: { '^/api': '' },
    secure: false,
  },
} : {};

export default {
  debug: true,
  /*
  ** Nuxt runtime config
  ** See https://nuxtjs.org/api/configuration-runtime-config
  ** See https://axios.nuxtjs.org/options.html#runtime-config
  */
  publicRuntimeConfig: {
    apiUrl: process.env.API_BASE_URL || 'https://api.nawhas.test/',
    searchHost: process.env.SEARCH_HOST || 'https://search.nawhas.test',

    axios: {
      browserBaseURL: process.env.API_BASE_URL || 'https://api.nawhas.test/',
    },
  },

  privateRuntimeConfig: {
    ignoreSslErrors: process.env.APP_ENV === 'development',

    axios: {
      baseURL: process.env.SERVER_API_BASE_URL || process.env.API_BASE_URL,
    },
  },
  /*
  ** Nuxt rendering mode
  ** See https://nuxtjs.org/api/configuration-mode
  */
  mode: 'universal',
  /*
  ** Nuxt target
  ** See https://nuxtjs.org/api/configuration-target
  */
  target: 'server',
  /*
  ** Headers of the page
  ** See https://nuxtjs.org/api/configuration-head
  */
  head: {
    titleTemplate: '%s | ' + TITLE_SUFFIX,
    title: 'Home',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: DEFAULT_DESCRIPTION },
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
    ],
  },
  /*
  ** PWA Module Configuration
  ** See https://pwa.nuxtjs.org/
  */
  pwa: {
    manifest: {
      name: 'Nawhas',
      short_name: 'Nawhas',
      description: 'Browse the largest collection of Nawhas online',
      theme_color: '#da0000',
      background_color: '#ff7252',
    },
    meta: {
      name: 'Nawhas',
      author: 'Nawhas.com',
      description: 'Browse the largest collection of Nawhas online',
      theme_color: '#da0000',
      ogHost: process.env.APP_DOMAIN,
      ogType: 'website',
      ogTitle: false,
      ogDescription: false,
    },
    workbox: {
      config: { debug: process.env.APP_ENV === 'development' },
      skipWaiting: false,
      clientsClaim: false,
    },
  },
  /*
  ** Global CSS
  */
  css: [
    { src: '@/assets/app.scss', lang: 'scss' },
  ],
  /*
  ** Plugins to load before mounting the App
  ** https://nuxtjs.org/guide/plugins
  */
  plugins: [
    '@/plugins/axios',
    '@/plugins/filters',
    '@/plugins/api',
    '@/plugins/search',
    '@/plugins/theme.client',
    '@/plugins/service-worker.client',
  ],
  /*
  ** Auto import components
  ** See https://nuxtjs.org/api/configuration-components
  */
  components: true,
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
    '@nuxt/typescript-build',
    '@nuxtjs/vuetify',
    '@nuxtjs/google-fonts',
    '@nuxtjs/svg',
    '@nuxtjs/router-extras',
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/universal-storage',
    '@nuxtjs/proxy',
    '@nuxtjs/axios',
    '@nuxtjs/pwa',
  ],
  /*
  ** Axios module configuration
  ** See https://axios.nuxtjs.org/options
  */
  axios: {
    credentials: true,
    headers: {
      common: {
        Accept: 'application/json',
      },
      delete: {},
      get: {},
      head: {},
      post: {
        'Content-Type': 'application/json',
      },
      put: {
        'Content-Type': 'application/json',
      },
      patch: {
        'Content-Type': 'application/json',
      },
    },
  },
  /*
  ** vuetify module configuration
  ** https://github.com/nuxt-community/vuetify-module
  */
  vuetify: {
    optionsPath: '@/vuetify.options.ts',
    defaultAssets: false,
    customVariables: ['@/assets/variables.scss'],
  },
  /*
  ** Google Fonts
  ** See https://github.com/nuxt-community/google-fonts-module
  */
  googleFonts: {
    families: {
      'Bellefair': true,
      'Roboto': [100, 200, 300, 400, 700],
      'Roboto Slab': [100, 300, 400, 700],
      'Roboto Mono': [100, 300, 400, 700],
      'Material Icons': true,
      'Material Icons Outlined': true,
    },
  },
  /*
  ** Build configuration
  ** See https://nuxtjs.org/api/configuration-build/
  */
  build: {
  },
  /*
  ** Loading Bar Configuration
  ** See https://nuxtjs.org/api/configuration-loading/
  */
  loading: {
    color: 'orangered',
    height: '3px',
  },

  router: {
    middleware: [
      'initialize',
    ],
    extendRoutes(routes) {
      routes.push(...[
        {
          name: 'albums.index',
          path: '/reciters/:id/albums',
          redirect: { path: '/reciters/:id' },
        },
        {
          name: 'tracks.index',
          path: '/reciters/:reciterId/albums/:albumId/tracks',
          redirect: { path: '/reciters/:id/albums/:albumId' },
        },
      ]);
    },
  },

  server: {
    https,
  },

  /*
  ** Google Fonts
  ** See https://github.com/nuxt-community/universal-storage-module#usage
  */
  storage: {
    initialState: {
      'theme:preference': 'auto',
      'theme:applied': 'light',
    },
  },

  /*
   ** Page Transition Settings
   ** See https://nuxtjs.org/guides/features/transitions/#the-pagetransition-property
   */
  pageTransition: {
    name: 'slide-x-transition',
    mode: 'out-in',
  },

  /*
   ** Proxy Settings
   ** See https://github.com/nuxt-community/proxy-module#proxy
   */
  proxy,
};

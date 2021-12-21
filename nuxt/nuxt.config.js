import path from 'path';
import fs from 'fs';

const APP_VERSION = (() => {
  try {
    return fs.readFileSync('.version', 'utf8');
  } catch (e) {
    // eslint-disable-next-line no-console
    console.log('No version file found.');
    return 'unreleased';
  }
})();

const TITLE_SUFFIX = 'Nawhas.com';
const TITLE_TEMPLATE = '%s | ' + TITLE_SUFFIX;
const DEFAULT_DESCRIPTION = 'Welcome to Nawhas.com, the most advanced library of nawhas online. ' +
  'Browse thousands of nawhas from hundreds of reciters, including thousands of nawha write-ups (lyrics).';

const proxy = process.env.BUILD_PROXY_API ? {
  // For local dev only
  '/api': {
    target: 'https://api.nawhas.test/',
    pathRewrite: { '^/api': '' },
    secure: false,
  },
} : {};

export default {
  debug: process.env.BUILD_APP_ENV !== 'production',
  /*
  ** Nuxt runtime config
  ** See https://nuxtjs.org/api/configuration-runtime-config
  ** See https://axios.nuxtjs.org/options.html#runtime-config
  */
  publicRuntimeConfig: {
    release: APP_VERSION || 'unreleased',
    apiUrl: process.env.API_BASE_URL,
    searchHost: process.env.SEARCH_HOST,

    axios: {
      browserBaseURL: process.env.API_BASE_URL,
    },
  },

  privateRuntimeConfig: {
    ignoreSslErrors: process.env.IGNORE_SSL_ERRORS || process.env.BUILD_APP_ENV === 'development',

    axios: {
      baseURL: process.env.SERVER_API_BASE_URL || process.env.API_BASE_URL,
    },
  },
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
    titleTemplate: TITLE_TEMPLATE,
    title: 'Home',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: DEFAULT_DESCRIPTION },
      { hid: 'og:title', property: 'og:title', content: 'Home', template: TITLE_TEMPLATE },
      { hid: 'og:description', name: 'og:description', content: DEFAULT_DESCRIPTION },
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
      { rel: 'preconnect', href: 'https://cdn2.nawhas.com' },
      { rel: 'preconnect', href: process.env.API_BASE_URL },
      { rel: 'preconnect', href: process.env.SEARCH_HOST },
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
      config: { debug: process.env.BUILD_APP_ENV === 'development' },
      workboxExtensions: [
        '@/static/sw-extensions/skip-waiting.js',
      ],
      swDest: path.resolve(__dirname, 'static', 'service-worker.js'),
      swURL: 'service-worker.js',
      skipWaiting: false,
      clientsClaim: false,
      runtimeCaching: [
        {
          urlPattern: 'https://cdn2.nawhas.com/.*',
        },
      ],
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
    '@/plugins/errors',
    '@/plugins/search',
    '@/plugins/theme.client',
    '@/plugins/service-worker.client',
    '@/plugins/lazy-images.client',
    { src: '@/plugins/youtube.ts', ssr: false },
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
    '@nuxtjs/sentry',
    '@nuxtjs/gtm',
  ],
  /*
  ** Axios module configuration
  ** See https://axios.nuxtjs.org/options
  */
  axios: {
    debug: !!process.env.BUILD_AXIOS_DEBUG,
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
   ** Google Tag Manager
   ** See https://github.com/nuxt-community/gtm-module#runtime-config
   */
  gtm: {
    id: 'GTM-5DF2SQB',
  },
  /*
  ** Build configuration
  ** See https://nuxtjs.org/api/configuration-build/
  */
  build: {
    parallel: true,
    cache: true,
    hardSource: true,
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
        {
          name: 'moderator.index',
          path: '/moderator',
          redirect: { path: '/moderator/revisions' },
        },
      ]);
    },
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

  /*
   ** Sentry Settings
   ** See https://github.com/nuxt-community/sentry-module
   */
  sentry: {
    dsn: process.env.BUILD_SENTRY_DSN || '',
    publishRelease: process.env.BUILD_SENTRY_PUBLISH_RELEASE || false,
    sourceMapStyle: 'hidden-source-map',
    config: {
      environment: process.env.BUILD_APP_ENV,
      release: APP_VERSION,
    },
  },

  /*
   ** Image Optimization Settings
   ** See https://marquez.co/docs/nuxt-optimized-images/configuration/
   */
  optimizedImages: {
    optimizeImages: true,
    handleImages: ['jpeg', 'jpg', 'png', 'svg', 'webp', 'gif'],
  },
};

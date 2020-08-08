require('dotenv').config();

const TITLE_SUFFIX = 'Nawhas.com';
const DEFAULT_DESCRIPTION = 'Welcome to Nawhas.com, the most advanced library of nawhas online.';

export default {
  debug: true,
  /*
  ** Nuxt runtime config
  ** See https://nuxtjs.org/api/configuration-runtime-config
  ** See https://axios.nuxtjs.org/options.html#runtime-config
  */
  publicRuntimeConfig: {
    apiUrl: process.env.API_BASE_URL || 'https://api.nawhas.test/',

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
    titleTemplate: '%s - ' + TITLE_SUFFIX,
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
  */
  pwa: {
    meta: {
      ogSiteName: false,
      ogType: false,
      ogTitle: false,
      ogDescription: false,
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
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    // Doc: https://axios.nuxtjs.org/usage
    '@nuxtjs/axios',
    '@nuxtjs/pwa',
  ],
  /*
  ** Axios module configuration
  ** See https://axios.nuxtjs.org/options
  */
  axios: {
    debug: false,
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
    optionsPath: '@/vuetify.options.js',
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
  },
};

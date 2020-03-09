import Vue from 'vue';
import VueGtag from 'vue-gtag';
import { sync } from 'vuex-router-sync';
import App from './App.vue';
import './registerServiceWorker';
import router from './router';
import store from './store';
import vuetify from './plugins/vuetify';
import './plugins/progress';
import './plugins/algolia';
import './filters';

Vue.config.productionTip = false;

Vue.use(VueGtag, {
  config: { id: 'UA-160025735-1' },
  enabled: process.env.VUE_APP_GTAG_ENABLED === true,
}, router);

sync(store, router);

new Vue({
  router,
  store,
  vuetify,
  render: (h) => h(App),
}).$mount('#app');

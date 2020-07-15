import Vue from 'vue';
import axios from 'axios';
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
import { Actions as FeaturesActions } from './store/modules/features';
import { Actions as AuthActions } from './store/modules/auth';

axios.defaults.withCredentials = true;

Vue.config.productionTip = false;

Vue.use(VueGtag, {
  config: { id: 'UA-160025735-1' },
  enabled: process.env.VUE_APP_GTAG_ENABLED === 'true',
}, router);

sync(store, router);

const bootstrap = () => Promise.all([
  store.dispatch(FeaturesActions.Fetch),
  store.dispatch(AuthActions.Check),
]);

bootstrap().then(() => new Vue({
  router,
  store,
  vuetify,
  render: (h) => h(App),
}).$mount('#app'));

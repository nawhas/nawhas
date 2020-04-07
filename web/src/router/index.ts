import Vue from 'vue';
import VueRouter from 'vue-router';
import Public from '@/layouts/Public.vue';
import LyricsPrint from '@/layouts/LyricsPrint.vue';
import goTo from 'vuetify/es5/services/goto';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    component: Public,
    children: [
      {
        path: '',
        name: 'Home',
        component: () => import(/* webpackChunkName: "home" */'@/views/public/HomeView.vue'),
      },
      {
        path: 'reciters',
        name: 'reciters.index',
        component: () => import(/* webpackChunkName: "reciters" */'@/views/public/reciters/RecitersView.vue'),
      },
      {
        path: 'reciters/:reciter',
        name: 'reciters.show',
        component: () => import(/* webpackChunkName: "reciter" */'@/views/public/reciters/ReciterView.vue'),
      },
      {
        path: 'reciters/:reciter/albums',
        name: 'albums.index',
        redirect: { name: 'reciters.show' },
      },
      {
        path: 'reciters/:reciter/albums/:album',
        name: 'albums.show',
        props: true,
        component: () => import(/* webpackChunkName: "albums" */'@/views/public/albums/AlbumView.vue'),
      },
      {
        path: 'reciters/:reciter/albums/:album/tracks',
        name: 'tracks.index',
        redirect: { name: 'albums.show' },
      },
      {
        path: 'reciters/:reciter/albums/:album/tracks/:track',
        name: 'tracks.show',
        props: true,
        component: () => import(/* webpackChunkName: "tracks" */'@/views/public/tracks/TrackView.vue'),
      },
      {
        path: 'about',
        name: 'About',
        component: () => import(/* webpackChunkName: "about" */ '@/views/public/AboutView.vue'),
      },
    ],
  },
  {
    path: '/print/:reciter/:album/:track',
    name: 'print.lyrics',
    component: LyricsPrint,
    props: true,
  },
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
  scrollBehavior(to, from, savedPosition) {
    let scrollTo: number|string = 0;

    if (to.hash) {
      scrollTo = to.hash;
    } else if (savedPosition) {
      scrollTo = savedPosition.y;
    }

    return goTo(scrollTo);
  },
});

export default router;

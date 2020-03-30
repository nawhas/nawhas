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
        component: () => import(/* webpackChunkName: "home" */'@/views/public/Home.vue'),
      },
      {
        path: 'reciters',
        name: 'reciters.index',
        component: () => import(/* webpackChunkName: "reciters" */'@/views/public/reciters/Index.vue'),
      },
      {
        path: 'reciters/:reciter',
        name: 'reciters.show',
        component: () => import(/* webpackChunkName: "reciter" */'@/views/public/reciters/Show.vue'),
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
        component: () => import(/* webpackChunkName: "albums" */'@/views/public/albums/Show.vue'),
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
        component: () => import(/* webpackChunkName: "tracks" */'@/views/public/tracks/Show.vue'),
      },
      {
        path: 'reciters/:reciter/albums/:album/tracks/:track/edit',
        name: 'tracks.edit',
        props: true,
        component: () => import(/* webpackChunkName: "tracks" */'@/views/public/tracks/Edit.vue'),
      },
      {
        path: 'about',
        name: 'About',
        // route level code-splitting
        // this generates a separate chunk (about.[hash].js) for this route
        // which is lazy-loaded when the route is visited.
        component: () => import(/* webpackChunkName: "about" */ '@/views/public/About.vue'),
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

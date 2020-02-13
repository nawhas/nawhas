import Vue from 'vue';
import VueRouter from 'vue-router';
import Public from '@/layouts/Public.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    component: Public,
    children: [
      {
        path: '',
        name: 'Home',
        component: () => import(/* webpackChunkName: "home" */'@/views/Home.vue'),
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
        path: 'reciters/:reciter/albums/:album/tracks/:track',
        name: 'tracks.show',
        component: () => import(/* webpackChunkName: "tracks" */'@/views/public/tracks/Show.vue'),
      },
    ],
  },
  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue'),
  },
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
});

export default router;

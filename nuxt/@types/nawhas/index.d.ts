import { InjectedApiPlugin } from '@/plugins/api';

declare module 'vue/types/vue' {
  interface Vue {
    $api: InjectedApiPlugin;
  }
}

declare module '@nuxt/types' {
  interface NuxtAppOptions {
    $api: InjectedApiPlugin;
  }

  interface Context {
    $api: InjectedApiPlugin;
  }
}

declare module 'vuex/types/index' {
  interface Store<S> {
    $api: InjectedApiPlugin;
  }
}

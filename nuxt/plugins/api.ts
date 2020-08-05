import { Plugin } from '@nuxt/types';
import { RecitersApi } from '@/api/reciters';

declare module 'vue/types/vue' {
  interface Vue {
    $api: InjectedApiPlugin;
  }
}

declare module '@nuxt/types' {
  interface NuxtAppOptions {
    $api: InjectedApiPlugin;
  }
}

declare module 'vuex/types/index' {
  interface Store<S> {
    $api: InjectedApiPlugin;
  }
}

export interface InjectedApiPlugin {
  reciters: RecitersApi;
}

const ApiPlugin: Plugin = ({ $axios }, inject) => {
  const api: InjectedApiPlugin = {
    reciters: new RecitersApi($axios),
  };

  inject('api', api);
};

export default ApiPlugin;

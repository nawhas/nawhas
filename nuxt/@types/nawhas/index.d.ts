import { InjectedApiPlugin } from '@/plugins/api';
import MeiliSearch from 'meilisearch';

declare module 'vue/types/vue' {
  interface Vue {
    $api: InjectedApiPlugin;
    $search: MeiliSearch;
  }
}

declare module '@nuxt/types' {
  interface NuxtAppOptions {
    $api: InjectedApiPlugin;
    $search: MeiliSearch;
  }

  interface Context {
    $api: InjectedApiPlugin;
    $search: MeiliSearch;
  }
}

declare module 'vuex/types/index' {
  interface Store<S> {
    $api: InjectedApiPlugin;
    $search: MeiliSearch;
  }
}

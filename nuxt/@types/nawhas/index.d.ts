import { InjectedApiPlugin } from '@/plugins/api';
import { InjectedErrorsPlugin } from '@/plugins/errors';
import MeiliSearch from 'meilisearch';

declare module 'vue/types/vue' {
  interface Vue {
    $api: InjectedApiPlugin;
    $errors: InjectedErrorsPlugin;
    $search: MeiliSearch;
  }
}

declare module '@nuxt/types' {
  interface NuxtAppOptions {
    $api: InjectedApiPlugin;
    $errors: InjectedErrorsPlugin;
    $search: MeiliSearch;
  }

  interface Context {
    $api: InjectedApiPlugin;
    $errors: InjectedErrorsPlugin;
    $search: MeiliSearch;
  }
}

declare module 'vuex/types/index' {
  interface Store<S> {
    $api: InjectedApiPlugin;
    $errors: InjectedErrorsPlugin;
    $search: MeiliSearch;
  }
}

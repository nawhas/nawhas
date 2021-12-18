import { MeiliSearch } from 'meilisearch';
import { InjectedApiPlugin } from '@/plugins/api';
import { InjectedErrorsPlugin } from '@/plugins/errors';

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
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  interface Store<S> {
    $api: InjectedApiPlugin;
    $errors: InjectedErrorsPlugin;
    $search: MeiliSearch;
  }
}

import { Plugin } from '@nuxt/types';
import MeiliSearch from 'meilisearch';

interface KeyResponse {
  data: string;
}

const SearchPlugin: Plugin = async ({ $axios, $config }, inject) => {
  const response = await $axios.$get<KeyResponse>('v1/search/key');

  inject('search', new MeiliSearch({
    host: $config.searchHost,
    apiKey: response.data,
  }));
};

export default SearchPlugin;

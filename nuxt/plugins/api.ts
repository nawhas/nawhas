import { Plugin } from '@nuxt/types';
import { RecitersApi } from '@/api/reciters';

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

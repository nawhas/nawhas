import { Plugin } from '@nuxt/types';
import { RecitersApi } from '@/api/reciters';
import { AlbumsApi } from '@/api/albums';
import { TracksApi } from '@/api/tracks';
import { AuthApi } from '@/api/auth';

export interface InjectedApiPlugin {
  reciters: RecitersApi;
  albums: AlbumsApi;
  tracks: TracksApi;
  auth: AuthApi;
}

const ApiPlugin: Plugin = ({ $axios }, inject) => {
  const api: InjectedApiPlugin = {
    reciters: new RecitersApi($axios),
    albums: new AlbumsApi($axios),
    tracks: new TracksApi($axios),
    auth: new AuthApi($axios),
  };

  inject('api', api);
};

export default ApiPlugin;

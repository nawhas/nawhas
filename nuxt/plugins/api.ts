import { Plugin } from '@nuxt/types';
import { RecitersApi } from '@/api/reciters';
import { AlbumsApi } from '@/api/albums';
import { TracksApi } from '@/api/tracks';
import { AuthApi } from '@/api/auth';
import { FeaturesApi } from '@/api/features';
import { LibraryApi } from '@/api/library';

export interface InjectedApiPlugin {
  reciters: RecitersApi;
  albums: AlbumsApi;
  tracks: TracksApi;
  auth: AuthApi;
  features: FeaturesApi;
  library: LibraryApi
}

const ApiPlugin: Plugin = ({ $axios, req }, inject) => {
  const api: InjectedApiPlugin = {
    reciters: new RecitersApi($axios),
    albums: new AlbumsApi($axios),
    tracks: new TracksApi($axios),
    auth: new AuthApi($axios, req),
    features: new FeaturesApi($axios),
    library: new LibraryApi($axios),
  };

  inject('api', api);
};

export default ApiPlugin;

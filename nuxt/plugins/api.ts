import { Plugin } from '@nuxt/types';
import { RecitersApi } from '@/api/reciters';
import { AlbumsApi } from '@/api/albums';
import { TracksApi } from '@/api/tracks';
import { AuthApi } from '@/api/auth';
import { FeaturesApi } from '@/api/features';
import { LibraryApi } from '@/api/library';
import { RevisionsApi } from '@/api/revisions';
import { DraftLyricsApi } from '@/api/draftLyrics';

export interface InjectedApiPlugin {
  reciters: RecitersApi;
  albums: AlbumsApi;
  tracks: TracksApi;
  auth: AuthApi;
  features: FeaturesApi;
  library: LibraryApi;
  revisions: RevisionsApi;
  draftLyrics: DraftLyricsApi;
}

const ApiPlugin: Plugin = ({ $axios }, inject) => {
  const api: InjectedApiPlugin = {
    reciters: new RecitersApi($axios),
    albums: new AlbumsApi($axios),
    tracks: new TracksApi($axios),
    auth: new AuthApi($axios),
    features: new FeaturesApi($axios),
    library: new LibraryApi($axios),
    revisions: new RevisionsApi($axios),
    draftLyrics: new DraftLyricsApi($axios),
  };

  inject('api', api);
};

export default ApiPlugin;

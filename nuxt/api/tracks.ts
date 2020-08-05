import { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  PersistedEntity,
  TimestampedEntity,
  createParams,
  useIncludes,
} from '@/api/common';
import { Reciter } from '@/api/reciters';
import { Album } from '@/api/albums';

/*
 * Entity Definitions
 */
export interface Track extends PersistedEntity, TimestampedEntity {
  reciter: Reciter;
  album: Album;
  title: string;
  slug: string;
  audio: string;
  lyrics: string;
}

/*
 * These are the available options for 'include'.
 */
export enum TrackIncludes {
  related = 'related',
  reciter = 'reciter',
  album = 'album',
}

/*
 * Request Options
 */
interface GetRequestOptions {
  include?: Array<TrackIncludes>;
}
interface IndexRequestOptions {
  include?: Array<TrackIncludes>;
}

/*
 * Response Definitions
 */
export interface TracksIndexResponse {}

export class TracksApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async get(reciterId: string, albumId: string, trackId: string, options: GetRequestOptions): Promise<Track> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios
      .$get<Track>(`v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}`, { params });
  }

  async index(reciterId: string, albumId: string, options: IndexRequestOptions): Promise<TracksIndexResponse> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<TracksIndexResponse>(
      `v1/reciters/${reciterId}/albums/${albumId}/tracks`,
      { params },
    );
  }

  async popular(options: IndexRequestOptions): Promise<TracksIndexResponse> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<TracksIndexResponse>(
      'v1/popular/tracks',
      { params },
    );
  }
}

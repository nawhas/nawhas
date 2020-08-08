import { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  PersistedEntity,
  TimestampedEntity,
  createParams,
  useIncludes,
  PaginatedResponse,
  usePagination,
  PaginationOptions, EntityCollection,
} from '@/api/common';
import { Reciter } from '@/api/reciters';
import { Album } from '@/api/albums';

export enum LyricsDocumentFormat {
  PLAIN_TEXT = 1,
  JSON_V1 = 2,
}

/*
 * Entity Definitions
 */
export interface Lyrics {
  content: string;
  format: LyricsDocumentFormat;

  /** @deprecated */
  id: string;
  /** @deprecated */
  trackId: string;
  /** @deprecated */
  createdAt: string;
  /** @deprecated */
  updatedAt: string;
}

export interface Media {
  uri: string;
  type: 'audio';
  provider: 'file';

  /** @deprecated */
  id: string;
  /** @deprecated */
  createdAt: string;
  /** @deprecated */
  updatedAt: string;
}

export interface Track extends PersistedEntity, TimestampedEntity {
  title: string;
  slug: string;
  year: string;
  reciterId: string;
  albumId: string;
  reciter?: Reciter;
  album?: Album;
  media?: EntityCollection<Media>;
  lyrics?: Lyrics | null;
  related?: { lyrics: boolean, audio: boolean };
}

/*
 * These are the available options for 'include'.
 */
export enum TrackIncludes {
  reciter = 'reciter',
  album = 'album',
  lyrics = 'lyrics',
  media = 'media',
  related = 'related',
}

/*
 * Request Options
 */
interface GetRequestOptions {
  include?: Array<TrackIncludes>;
}
interface IndexRequestOptions {
  include?: Array<TrackIncludes>;
  pagination?: PaginationOptions;
}

/*
 * Response Definitions
 */
export interface TracksIndexResponse extends PaginatedResponse<Track> {}

export class TracksApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async get(reciterId: string, albumId: string, trackId: string, options: GetRequestOptions = {}): Promise<Track> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<Track>(
      `v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}`,
      { params },
    );
  }

  async index(reciterId: string, albumId: string, options: IndexRequestOptions = {}): Promise<TracksIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<TracksIndexResponse>(
      `v1/reciters/${reciterId}/albums/${albumId}/tracks`,
      { params },
    );
  }

  async popular(options: IndexRequestOptions = {}): Promise<TracksIndexResponse> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<TracksIndexResponse>(
      'v1/popular/tracks',
      { params },
    );
  }
}

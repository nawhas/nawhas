import { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  createParams,
  useIncludes,
  PaginatedResponse,
  usePagination,
  PaginationOptions,
} from '@/api/common';
import { Format as LyricsDocumentFormat } from '@/entities/lyrics';
import { Track } from '@/entities/track';

/*
 * Request Payloads
 */
export interface StoreTrackPayload {
  title: string;
  lyrics: string;
  format?: LyricsDocumentFormat;
}
export interface UpdateTrackPayload {
  title?: string;
  lyrics?: string;
  format?: LyricsDocumentFormat;
}

/*
 * These are the available options for 'include'.
 */
export enum TrackIncludes {
  Reciter = 'reciter',
  Album = 'album',
  Lyrics = 'lyrics',
  Media = 'media',
  Related = 'related',
}

/*
 * Request Options
 */
export interface RequestOptions {
  include?: Array<TrackIncludes|string>;
}
export interface IndexRequestOptions extends RequestOptions {
  pagination?: PaginationOptions;
}
export interface PopularRequestOptions extends IndexRequestOptions {
  reciterId?: string;
}

/*
 * Response Definitions
 */
export interface TracksIndexResponse extends PaginatedResponse<Track> {}

export class TracksApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async index(reciterId: string, albumId: string, options: IndexRequestOptions = {}): Promise<TracksIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<TracksIndexResponse>(
      `v1/reciters/${reciterId}/albums/${albumId}/tracks`,
      { params },
    );
  }

  async popular(options: PopularRequestOptions = {}): Promise<TracksIndexResponse> {
    const params = createParams();
    useIncludes(params, options.include);
    usePagination(params, options.pagination);

    if (options.reciterId) {
      params.set('reciterId', options.reciterId);
    }

    return await this.axios.$get<TracksIndexResponse>(
      'v1/popular/tracks',
      { params },
    );
  }

  async get(reciterId: string, albumId: string, trackId: string, options: RequestOptions = {}): Promise<Track> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<Track>(
      `v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}`,
      { params },
    );
  }

  async store(
    reciterId: string,
    albumId: string,
    payload: StoreTrackPayload,
    options: RequestOptions = {},
  ): Promise<Track> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$post<Track>(
      `v1/reciters/${reciterId}/albums/${albumId}/tracks`,
      payload,
      { params },
    );
  }

  async update(
    reciterId: string,
    albumId: string,
    trackId: string,
    payload: UpdateTrackPayload,
    options: RequestOptions = {},
  ): Promise<Track> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$patch<Track>(
      `v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}`,
      payload,
      { params },
    );
  }

  async changeAudio(
    reciterId: string,
    albumId: string,
    trackId: string,
    audio: File,
    options: RequestOptions = {},
  ): Promise<Track> {
    const params = createParams();
    useIncludes(params, options.include);

    const formData = new FormData();
    formData.append('audio', audio);

    return await this.axios.$post<Track>(
      `v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}/media/audio`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        params,
      },
    );
  }

  async delete(reciterId: string, albumId: string, trackId: string): Promise<void> {
    await this.axios.delete(`v1/reciters/${reciterId}/albums/${albumId}/tracks/${trackId}`);
  }
}

/* TODO - model includes better
const includes: TrackIncludes = {
  reciter: {
    related: true,
  },
  album: {
    related: true,
    tracks: {
      lyrics: true,
      media: true,
    },
  },
};

interface ReciterIncludes {
  related?: boolean;
}

interface AlbumIncludes {
  related?: boolean;
  reciter?: ReciterIncludes;
  tracks?: boolean | TrackIncludes;
}

interface TrackIncludes {
  lyrics?: boolean;
  media?: boolean;
  reciter?: boolean | ReciterIncludes;
  album?: boolean | AlbumIncludes;
  related?: boolean;
}
*/

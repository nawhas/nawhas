import { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  PaginationOptions,
  PersistedEntity,
  TimestampedEntity,
  PaginatedResponse,
  createParams,
  usePagination,
  useIncludes,
  EntityCollection,
} from '@/api/common';
import { Reciter } from '@/api/reciters';
import { Track } from "@/api/tracks";

/*
 * Entity Definitions
 */
export interface Album extends PersistedEntity, TimestampedEntity {
  reciterId: string;
  title: string;
  year: string;
  artwork: string;
  reciter?: Reciter;
  tracks: EntityCollection<Track>;
}

/*
 * These are the available options for 'include'.
 */
export enum AlbumIncludes {
  related = 'related',
  reciter = 'reciter',
  tracks = 'tracks',
}

/*
 * Request Options
 */
interface GetRequestOptions {
  include?: Array<AlbumIncludes>;
}
interface IndexRequestOptions {
  include?: Array<AlbumIncludes>;
  pagination?: PaginationOptions;
}

/*
 * Response Definitions
 */
export interface AlbumsIndexResponse extends PaginatedResponse<Album> {}

export class AlbumsApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async get(reciterId: string, albumId: string, options: GetRequestOptions): Promise<Album> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios
      .$get<Album>(`v1/reciters/${reciterId}/albums/${albumId}`, { params });
  }

  async index(reciterId: string, options: IndexRequestOptions): Promise<AlbumsIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<AlbumsIndexResponse>(
      `v1/reciters/${reciterId}/albums`,
      { params },
    );
  }
}

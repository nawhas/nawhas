import {
  PaginationOptions,
  PaginatedResponse,
  createParams,
  usePagination,
  useIncludes,
} from '@/api/common';
import { Album } from '@/entities/album';

/*
 * Request Payloads
 */
export interface StoreAlbumPayload {
  title: string;
  year: string;
}
export interface UpdateAlbumPayload {
  title?: string;
  year?: string;
}

/*
 * These are the available options for 'include'.
 */
export enum AlbumIncludes {
  Related = 'related',
  Reciter = 'reciter',
  Tracks = 'tracks',
}

/*
 * Request Options
 */
interface GetRequestOptions {
  include?: Array<AlbumIncludes | string>;
}
interface IndexRequestOptions {
  include?: Array<AlbumIncludes | string>;
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

  async index(reciterId: string, options: IndexRequestOptions = {}): Promise<AlbumsIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<AlbumsIndexResponse>(
      `v1/reciters/${reciterId}/albums`,
      { params },
    );
  }

  async get(reciterId: string, albumId: string, options: GetRequestOptions = {}): Promise<Album> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<Album>(
      `v1/reciters/${reciterId}/albums/${albumId}`,
      { params },
    );
  }

  async store(reciterId: string, payload: StoreAlbumPayload): Promise<Album> {
    return await this.axios.$post<Album>(`v1/reciters/${reciterId}/albums`, payload);
  }

  async update(reciterId: string, albumId: string, payload: UpdateAlbumPayload): Promise<Album> {
    return await this.axios.$patch<Album>(`v1/reciters/${reciterId}/albums/${albumId}`, payload);
  }

  async changeArtwork(reciterId: string, albumId: string, artwork: File): Promise<Album> {
    const formData = new FormData();
    formData.append('artwork', artwork);

    return await this.axios.$post<Album>(`v1/reciters/${reciterId}/albums/${albumId}/artwork`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  }

  async delete(reciterId: string, albumId: string): Promise<void> {
    await this.axios.delete(`v1/reciters/${reciterId}/albums/${albumId}`);
  }
}

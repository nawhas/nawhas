import { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  PaginationOptions,
  PaginatedResponse,
  createParams,
  usePagination,
  useIncludes,
} from '@/api/common';
import { Track } from '@/entities/track';
import { TrackIncludes, TracksIndexResponse } from '@/api/tracks';

/*
 * Request Payloads
 */
export interface TracksPayload {
  ids: Array<string>;
}

/*
 * Request Options
 */
interface IndexRequestOptions {
  include?: Array<TrackIncludes>;
  pagination?: PaginationOptions;
  sortBy?: string;
  sortDir?: 'asc'|'desc';
}

/*
 * Response Definitions
 */
export interface LibraryTracksResponse extends PaginatedResponse<Track> {}

export class LibraryApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async tracks(options: IndexRequestOptions = {}): Promise<LibraryTracksResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<TracksIndexResponse>('/me/tracks', { params });
  }

  async saveTrack(payload: TracksPayload): Promise<void> {
    await this.axios.$put('/me/tracks', payload);
  }

  async removeTrack(payload: TracksPayload): Promise<void> {
    await this.axios.$delete('/me/tracks', { data: payload });
  }
}

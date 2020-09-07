import { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  PaginationOptions,
  PaginatedResponse,
  createParams,
  usePagination,
} from '@/api/common';
import { Revision } from '@/entities/revision';

/*
 * Request Options
 */
interface IndexRequestOptions {
  pagination?: PaginationOptions;
}

/*
 * Response Definitions
 */
export interface RevisionsIndexResponse extends PaginatedResponse<Revision> {}

export class RevisionsApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async index(options: IndexRequestOptions = {}): Promise<RevisionsIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);

    return await this.axios.$get<RevisionsIndexResponse>('/v1/revisions', { params });
  }
}

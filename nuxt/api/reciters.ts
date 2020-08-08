import { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  PaginationOptions,
  PersistedEntity,
  TimestampedEntity,
  PaginatedResponse,
  createParams,
  usePagination,
  useIncludes,
} from '@/api/common';

/*
 * Entity Definitions
 */
export interface Reciter extends PersistedEntity, TimestampedEntity {
  name: string;
  description: string | null;
  slug: string;
  avatar: string | null;
  related?: { albums: number };
}

/*
 * These are the available options for 'include'.
 */
export enum ReciterIncludes {
  related = 'related'
}

/*
 * Request Options
 */
interface GetRequestOptions {
  include?: Array<ReciterIncludes>;
}
interface IndexRequestOptions {
  include?: Array<ReciterIncludes>;
  pagination?: PaginationOptions;
}

/*
 * Response Definitions
 */
export interface RecitersIndexResponse extends PaginatedResponse<Reciter> {}

export class RecitersApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async get(id: string, options: GetRequestOptions = {}): Promise<Reciter> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<Reciter>(
      `v1/reciters/${id}`,
      { params },
    );
  }

  async index(options: IndexRequestOptions = {}): Promise<RecitersIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<RecitersIndexResponse>(
      'v1/reciters',
      { params },
    );
  }

  async popular(options: IndexRequestOptions = {}): Promise<RecitersIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<RecitersIndexResponse>(
      'v1/popular/reciters',
      { params },
    );
  }
}

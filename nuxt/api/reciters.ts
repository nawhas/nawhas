import type { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  PaginationOptions,
  PaginatedResponse,
  createParams,
  usePagination,
  useIncludes,
} from '@/api/common';
import { Reciter } from '@/entities/reciter';

/*
 * Request Payloads
 */
export interface StoreReciterPayload {
  name: string;
  description?: string | null;
}
export interface UpdateReciterPayload {
  name?: string;
  description?: string | null;
}

/*
 * These are the available options for 'include'.
 */
export enum ReciterIncludes {
  Related = 'related'
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

  async index(options: IndexRequestOptions = {}): Promise<RecitersIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<RecitersIndexResponse>('v1/reciters', { params });
  }

  async popular(options: IndexRequestOptions = {}): Promise<RecitersIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<RecitersIndexResponse>('v1/popular/reciters', { params });
  }

  async get(id: string, options: GetRequestOptions = {}): Promise<Reciter> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<Reciter>(`v1/reciters/${id}`, { params });
  }

  async store(payload: StoreReciterPayload): Promise<Reciter> {
    return await this.axios.$post<Reciter>('v1/reciters', payload);
  }

  async update(id: string, payload: UpdateReciterPayload): Promise<Reciter> {
    return await this.axios.$patch<Reciter>(`v1/reciters/${id}`, payload);
  }

  async changeAvatar(id: string, avatar: File): Promise<Reciter> {
    const formData = new FormData();
    formData.append('avatar', avatar);

    return await this.axios.$post<Reciter>(`v1/reciters/${id}/avatar`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  }

  async delete(id: string): Promise<void> {
    await this.axios.delete(`v1/reciters/${id}`);
  }
}

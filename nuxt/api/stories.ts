import type { NuxtAxiosInstance } from '@nuxtjs/axios';
import { Story } from '@/entities/story';
import { createParams, PaginatedResponse, PaginationOptions, usePagination } from '@/api/common';

export interface StoriesIndexResponse extends PaginatedResponse<Story> {}

interface IndexOptions {
  pagination?: PaginationOptions;
}

export class StoriesApi {
  constructor(private axios: NuxtAxiosInstance) {}

  async index(options: IndexOptions = {}): Promise<StoriesIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    return await this.axios.$get<StoriesIndexResponse>('v1/stories', { params });
  }

  async show(slug: string): Promise<Story> {
    return await this.axios.$get<Story>(`v1/stories/${encodeURIComponent(slug)}`);
  }
}

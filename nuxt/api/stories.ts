import type { NuxtAxiosInstance } from '@nuxtjs/axios';
import { Story } from '@/entities/story';
import { createParams, PaginatedResponse, PaginationOptions, usePagination } from '@/api/common';

export interface StoriesIndexResponse extends PaginatedResponse<Story> {}

export interface StoreStoryPayload {
  title: string;
  slug?: string | null;
  excerpt?: string | null;
  body?: string | null;
  hero_image_url?: string | null;
  display_date?: string | null;
  published?: boolean;
}

export interface UpdateStoryPayload {
  title?: string;
  slug?: string | null;
  excerpt?: string | null;
  body?: string | null;
  hero_image_url?: string | null;
  display_date?: string | null;
  published?: boolean;
}

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

  /** Public read by slug, or moderator read by UUID. */
  async show(identifier: string): Promise<Story> {
    return await this.axios.$get<Story>(`v1/stories/${encodeURIComponent(identifier)}`);
  }

  async store(payload: StoreStoryPayload): Promise<Story> {
    return await this.axios.$post<Story>('v1/stories', payload);
  }

  async update(id: string, payload: UpdateStoryPayload): Promise<Story> {
    return await this.axios.$patch<Story>(`v1/stories/${encodeURIComponent(id)}`, payload);
  }

  async destroy(id: string): Promise<void> {
    await this.axios.delete(`v1/stories/${encodeURIComponent(id)}`);
  }

  /** Multipart upload; stores on the public disk like reciter avatar / album artwork. */
  async uploadHeroImage(id: string, file: File): Promise<Story> {
    const formData = new FormData();
    formData.append('hero_image', file);

    return await this.axios.$post<Story>(`v1/stories/${encodeURIComponent(id)}/hero`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  }
}

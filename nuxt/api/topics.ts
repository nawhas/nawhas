import { NuxtAxiosInstance } from '@nuxtjs/axios';
import {
  PaginationOptions,
  PaginatedResponse,
  createParams,
  usePagination,
  useIncludes,
} from '@/api/common';
import { Topic } from '@/entities/topic';

/*
 * These are the available options for 'include'.
 */
export enum TopicIncludes {
  Related = 'related'
}

/*
 * Request Options
 */
interface GetRequestOptions {
  include?: Array<TopicIncludes>;
}

interface IndexRequestOptions {
  include?: Array<TopicIncludes>;
  pagination?: PaginationOptions;
}

/*
 * Response Definitions
 */
export interface TopicsIndexResponse extends PaginatedResponse<Topic> {}

export class TopicsApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async index(options: IndexRequestOptions = {}): Promise<TopicsIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);
    useIncludes(params, options.include);

    return await this.axios.$get<TopicsIndexResponse>('v1/topics', { params });
  }

  // TODO - Make the popular request hit the correct endpoint
  async popular(options: IndexRequestOptions = {}): Promise<TopicsIndexResponse> {
    const params = createParams();
    usePagination(params, options.pagination);

    return await this.axios.$get<TopicsIndexResponse>('v1/topics', { params });
  }

  async get(id: string, options: GetRequestOptions = {}): Promise<Topic> {
    const params = createParams();
    useIncludes(params, options.include);

    return await this.axios.$get<Topic>(`v1/reciters/${id}`, { params });
  }
}

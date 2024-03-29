export interface PaginationOptions {
  limit?: number;
  page?: number;
}

export interface PaginationLinks {
  next?: string;
  previous?: string;
}

export interface PaginationMetadata {
  total: number;
  count: number;

  per_page: number;

  current_page: number;

  total_pages: number;
  links: PaginationLinks;
}

export interface ResponseMetadata {
  pagination: PaginationMetadata;
}

export interface PaginatedResponse<T> {
  data: Array<T>;
  meta: ResponseMetadata;
}

export type RequestParams = URLSearchParams;

export function createParams(): RequestParams {
  return new URLSearchParams();
}

export function usePagination(params: RequestParams, options: PaginationOptions | undefined) {
  if (options?.limit) {
    params.set('per_page', String(options.limit));
  }

  if (options?.page) {
    params.set('page', String(options.page));
  }
}

export function useIncludes(params: RequestParams, includes: Array<string> | undefined) {
  if (includes) {
    params.set('include', includes.join(','));
  }
}

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
  // eslint-disable-next-line camelcase
  per_page: number;
  // eslint-disable-next-line camelcase
  current_page: number;
  // eslint-disable-next-line camelcase
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

export function useSortDir(params: RequestParams, sortDir: 'asc'|'desc' | undefined) {
  if (sortDir) {
    params.set('sortDir', String(sortDir));
  }
}
export function useSortBy(params: RequestParams, sortBy: string | undefined) {
  if (sortBy) {
    params.set('sortBy', String(sortBy));
  }
}

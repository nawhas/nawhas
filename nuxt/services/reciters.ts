import { IncludeResourcesOptions, PaginatedIndexOptions } from '@/services/common';
import client from './client';

interface GetRecitersOptions extends PaginatedIndexOptions, IncludeResourcesOptions {}

interface GetReciterOptions extends IncludeResourcesOptions {}

/**
 * Get Reciters
 * @param {object} options
 * @param {int} [options.page]
 * @param {int} [options.per_page]
 *
 * @returns {Promise}
 */
export function getReciters(options: GetRecitersOptions = {}) {
  return client.get('/v1/reciters', options);
}

/**
 * Get a reciter
 * @param {string|int} reciter - Slug or ID of the reciter
 * @param {object} options
 *
 * @returns {Promise}
 */
export function getReciter(reciter, options: GetReciterOptions = {}) {
  return client.get(`/v1/reciters/${reciter}`, options);
}

export default {
  getReciters,
  getReciter,
};

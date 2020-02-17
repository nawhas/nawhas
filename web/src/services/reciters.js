import client from './client';

/**
 * Get Reciters
 * @param {object} options
 * @param {int} [options.page]
 * @param {int} [options.per_page]
 *
 * @returns {Promise}
 */
export function getReciters(options = {}) {
  return client.get('/v1/reciters', options);
}

/**
 * Get a reciter
 * @param {string|int} reciter - Slug or ID of the reciter
 * @param {object} options
 *
 * @returns {Promise}
 */
export function getReciter(reciter, options = {}) {
  return client.get(`/v1/reciters/${reciter}`, options);
}

export default {
  getReciters,
  getReciter,
};

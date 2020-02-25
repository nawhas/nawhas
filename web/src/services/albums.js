import client from './client';

/**
 * Get Albums
 * @param {string|int} reciter
 * @param {object} [options]
 * @param {int} [options.page]
 * @param {int} [options.limit]
 *
 * @returns {Promise}
 */
export function getAlbums(reciter, options = {}) {
  return client.get(`/v1/reciters/${reciter}/albums`, options);
}

/**
 * Get a Album
 * @param {string|int} reciter
 * @param {string} album
 * @param {object} [options]
 * @param {int} [options.page]
 * @param {int} [options.limit]
 *
 * @returns {Promise}
 */
export function getAlbum(reciter, album, options = {}) {
  return client.get(`/v1/reciters/${reciter}/albums/${album}`, options);
}

export default {
  getAlbums,
  getAlbum,
};

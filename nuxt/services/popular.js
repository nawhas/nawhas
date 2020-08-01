import client from './client';

/**
 * Get popular reciters
 * @param {object} options
 * @param {int} [options.limit]
 *
 * @returns {Promise}
 */
export function getPopularReciters(options = {}) {
  return client.get('/v1/popular/reciters', options);
}

/**
 * Get popular albums
 * @param {object} options
 * @param {int} [options.limit]
 * @param {string|id} [options.reciterId]
 *
 * @returns {Promise}
 */
export function getPopularAlbums(options = {}) {
  return client.get('/v1/popular/albums', options);
}

/**
 * Get popular tracks
 * @param {object} options
 * @param {int} [options.limit]
 * @param {string|id} [options.reciterId]
 * @param {string} [options.include]
 *
 * @returns {Promise}
 */
export function getPopularTracks(options = {}) {
  return client.get('/v1/popular/tracks', options);
}

export default {
  getPopularReciters,
  getPopularAlbums,
  getPopularTracks,
};

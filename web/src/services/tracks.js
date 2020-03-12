import client from './client';

/**
 * Get a single track
 * @param {string|int} reciter - Reciter ID
 * @param {string|int} album - Album ID
 * @param {object} [options]
 * @param {int} [options.page]
 * @param {int} [options.limit]
 *
 * @returns {Promise}
 */
export function getTracks(reciter, album, options = {}) {
  return client.get(`/v1/reciters/${reciter}/albums/${album}/tracks`, options);
}

/**
 * Get a single track
 * @param {string|int} reciter - Reciter ID
 * @param {string|int} album - Album ID
 * @param {string|int} track - Track ID
 * @param {object} [options]
 * @param {int} [options.page]
 * @param {int} [options.limit]
 *
 * @returns {Promise}
 */
export function getTrack(reciter, album, track, options = {}) {
  return client.get(`/v1/reciters/${reciter}/albums/${album}/tracks/${track}`, options);
}

export default {
  getTrack,
};

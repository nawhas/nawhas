import { TracksPayload } from '@/api/library';

export function saevTrack(vue: Vue, tracks: TracksPayload) {
  if (!isUserLoggedIn(vue)) {
    return false;
  }
  vue.$api.library.saveTrack(tracks);
}

function isUserLoggedIn(vue: Vue): Boolean {
  return vue.$store.getters['auth/user'];
}

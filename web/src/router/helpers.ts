import router from '@/router';

export async function goToTrack(reciter: string, album: string, track: string, props: object = {}) {
  return router.push({
    name: 'tracks.show',
    params: {
      reciter,
      album,
      track,
      ...props,
    },
  });
}

export async function goToReciter(reciter: string, props: object = {}) {
  return router.push({
    name: 'reciters.show',
    params: {
      reciter,
      ...props,
    },
  });
}

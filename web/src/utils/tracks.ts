export function hasAudioFile(track: any) {
  return track.related ? track.related.audio === true : false;
}

export function hasLyrics(track: any) {
  return track.related ? track.related.lyrics === true : false;
}

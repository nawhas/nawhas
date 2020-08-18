import { PersistedEntity, TimestampedEntity, EntityCollection } from '@/entities/common';
import { Reciter } from '@/entities/reciter';
import { Track } from '@/entities/track';

export interface Album extends PersistedEntity, TimestampedEntity {
  title: string;
  year: string;
  artwork: string | null;
  reciterId: string;
  reciter?: Reciter;
  tracks?: EntityCollection<Track>;
  related?: { tracks: number };
}

export function getAlbumUri(album: Album, reciter?: Reciter|null) {
  const slug = reciter?.slug ?? album.reciter?.slug;

  if (slug === undefined) {
    throw new Error('Unable to determine reciter.');
  }

  return `/reciters/${slug}/albums/${album.year}`;
}

export function getAlbumArtwork(album?: Album|null) {
  return album?.artwork ?? require('@/assets/img/defaults/default-album-image.png');
}

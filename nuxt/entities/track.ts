import { PersistedEntity, TimestampedEntity, EntityCollection } from '@/entities/common';
import { Reciter } from '@/entities/reciter';
import { Album } from '@/entities/album';
import { Lyrics } from '@/entities/lyrics';

export interface Media {
  uri: string;
  type: 'audio';
  provider: 'file';

  /** @deprecated */
  id: string;
  /** @deprecated */
  createdAt: string;
  /** @deprecated */
  updatedAt: string;
}

export interface Track extends PersistedEntity, TimestampedEntity {
  title: string;
  slug: string;
  year: string;
  reciterId: string;
  albumId: string;
  reciter?: Reciter;
  album?: Album;
  media?: EntityCollection<Media>;
  lyrics?: Lyrics | null;
  related?: { lyrics: boolean, audio: boolean };
}

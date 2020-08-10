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

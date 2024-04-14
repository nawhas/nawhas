import { PersistedEntity, TimestampedEntity } from '@/entities/common';
import { Lyrics } from '@/entities/lyrics';

export interface DraftLyrics extends PersistedEntity, TimestampedEntity {
  trackId: string;
  document: Lyrics
}

import { PersistedEntity, TimestampedEntity } from '@/entities/common';
import { LyricsDocument } from '@/entities/lyrics';

export interface DraftLyrics extends PersistedEntity, TimestampedEntity {
  trackId: string;
  document: LyricsDocument
}

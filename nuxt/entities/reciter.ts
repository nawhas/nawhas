import { PersistedEntity, TimestampedEntity } from '@/entities/common';

export interface Reciter extends PersistedEntity, TimestampedEntity {
  name: string;
  description: string | null;
  slug: string;
  avatar: string | null;
  related?: { albums: number };
}

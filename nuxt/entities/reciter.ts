import { PersistedEntity, TimestampedEntity } from '@/entities/common';

export interface Reciter extends PersistedEntity, TimestampedEntity {
  name: string;
  description: string | null;
  slug: string;
  avatar: string | null;
  related?: { albums: number };
}

export function getReciterUri(reciter: Reciter) {
  return `/reciters/${reciter.slug}`;
}

export function getReciterAvatar(reciter?: Reciter|null) {
  return reciter?.avatar ?? '/defaults/default-reciter-avatar.png';
}

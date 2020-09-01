import { PersistedEntity } from '@/entities/common';
import { User } from '@/entities/user';

export enum EntityType {
  Reciter = 'reciter',
  Album = 'album',
  Track = 'track',
}

interface Snapshot {
  [key: string]: any;
}

interface Metadata {
  link: string;
  [key: string]: string;
}

export interface Revision extends PersistedEntity {
  version: number;
  entityType: EntityType;
  entityId: string;
  previous: Snapshot | null;
  snapshot: Snapshot | null;
  meta: Metadata;
  user: User | null;
  createdAt: string;
}

import { PersistedEntity } from '@/entities/common';
import { User } from '@/entities/user';

export enum EntityType {
  RECITER = 'reciter',
  ALBUM = 'album',
  TRACK = 'track',
}

export interface Revision extends PersistedEntity {
  version: number;
  entityType: EntityType;
  entityId: string;
  previous: Map<string, any> | null;
  snapshot: Map<string, any>;
  meta: Map<string, any>;
  user: User | null;
  createdAt: string;
}

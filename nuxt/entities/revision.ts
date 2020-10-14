import { PersistedEntity } from '@/entities/common';
import { User } from '@/entities/user';

export enum EntityType {
  Reciter = 'reciter',
  Album = 'album',
  Track = 'track',
  Topic = 'topic',
}

export enum ChangeType {
  Created = 'created',
  Modified = 'modified',
  Deleted = 'deleted',
}

interface Snapshot {
  [key: string]: any;
}

interface Metadata {
  link: string | null;
  [key: string]: string | null;
}

export interface Revision extends PersistedEntity {
  version: number;
  entityType: EntityType;
  entityId: string;
  changeType: ChangeType;
  previous: Snapshot | null;
  snapshot: Snapshot;
  meta: Metadata;
  user: User | null;
  createdAt: string;
}

export function getUserDisplay(revision: Revision) {
  return revision.user?.email ?? 'system';
}

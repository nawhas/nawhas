import { Data as UserData } from './user';

interface EntitySnapshot {
  [key: string]: any;
}
export interface Data {
  id: string;
  type: ChangeType;
  user: UserData;
  entity: EntityType;
  entityId: string;
  old: EntitySnapshot | null;
  new: EntitySnapshot | null;
  createdAt: string | null;
  updatedAt: string | null;
}

export enum ChangeType {
  Created = 'created',
  Updated = 'updated',
  Deleted = 'deleted',
}

export enum EntityType {
  Reciter = 'reciter',
  Album = 'album',
  Track = 'track',
  Lyrics = 'lyrics'
}

export class Revision {
  constructor(private data: Data) { }

  get id(): string {
    return this.data.id;
  }

  get type(): ChangeType {
    return this.data.type;
  }

  get user(): UserData {
    return this.data.user;
  }

  get entity(): EntityType {
    return this.data.entity;
  }

  get entityId(): string {
    return this.data.entityId;
  }

  get old(): EntitySnapshot | null {
    return this.data.old;
  }

  get new(): EntitySnapshot | null {
    return this.data.new;
  }

  get createdAt(): string | null {
    return this.data.createdAt;
  }

  get updatedAt(): string | null {
    return this.data.updatedAt;
  }
}

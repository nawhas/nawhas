import { User } from './user';

export interface Data {
  id: string;
  type: ChangeType;
  user: User;
  entity: Entity;
  entityId: string;
  old?: any;
  new?: any;
  createdAt?: string;
  updatedAt?: string;
}

export enum ChangeType {
  Created = 'created',
  Modified = 'modified',
  Deleted = 'deleted',
}

export enum Entity {
  Reciter = 'reciter',
  Album = 'album',
  Track = 'track',
  Lyrics = 'lyrics'
}

export class Audit {
  constructor(private data: Data) { }

  get id(): string {
    return this.data.id;
  }

  get type(): ChangeType {
    return this.data.type;
  }

  get user(): User {
    return this.data.user;
  }

  get entity(): Entity {
    return this.data.entity;
  }

  get entityId(): string {
    return this.data.entityId;
  }

  get old(): any | undefined {
    return this.data.old;
  }

  get new(): any | undefined {
    return this.data.new;
  }

  get createdAt(): string | undefined {
    return this.data.createdAt;
  }

  get updatedAt(): string | undefined {
    return this.data.updatedAt;
  }
}

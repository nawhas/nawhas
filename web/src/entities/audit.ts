import { User } from './user';

export interface Data {
  id: string;
  type: ChangeType;
  user: User;
  entity: string;
  entityId: string;
  old?: Array<object>;
  new?: Array<object>;
  createdAt?: string;
  updatedAt?: string;
}

export enum ChangeType {
  Created = 'creaded',
  Modified = 'modified',
  Deleted = 'deleted',
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

  get entity(): string {
    return this.data.entity;
  }

  get entityId(): string {
    return this.data.entityId;
  }

  get old(): Array<object> | undefined {
    return this.data.old;
  }

  get new(): Array<object> | undefined {
    return this.data.new;
  }

  get createdAt(): string | undefined {
    return this.data.createdAt;
  }

  get updatedAt(): string | undefined {
    return this.data.updatedAt;
  }
}

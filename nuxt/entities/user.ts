import { PersistedEntity, TimestampedEntity } from '@/entities/common';

export enum Role {
  Contributor = 'contributor',
  Moderator = 'moderator',
  Guest = 'guest',
}

export interface User extends PersistedEntity, TimestampedEntity {
  name: string;
  avatar: string;
  email: string;
  role: Role;
  nickname: string | null;
}

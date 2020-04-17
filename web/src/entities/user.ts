export interface Data {
  id: string;
  name: string;
  nickname: string|null;
  avatar: string;
  email: string;
  role: Role;
  createdAt?: string;
  updatedAt?: string;
}

export enum Role {
  Moderator = 'moderator',
  Contributor = 'contributor',
  Guest = 'guest',
}

export class User {
  constructor(private data: Data) {}

  get id(): string {
    return this.data.id;
  }

  get name(): string {
    return this.data.name;
  }

  get nickname(): string|null {
    return this.data.nickname;
  }

  get avatar(): string {
    return this.data.avatar;
  }

  get email(): string {
    return this.data.email;
  }

  get role(): Role {
    return this.data.role;
  }

  get createdAt(): string|undefined {
    return this.data.createdAt;
  }

  get updatedAt(): string|undefined {
    return this.data.updatedAt;
  }
}

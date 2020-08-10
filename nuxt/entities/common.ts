export interface TimestampedEntity {
  createdAt: string;
  updatedAt: string;
}

export interface PersistedEntity {
  id: string;
}

export interface EntityCollection<T> {
  data: Array<T>;
}

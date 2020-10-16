import { PersistedEntity, TimestampedEntity } from '@/entities/common';

export interface Topic extends PersistedEntity, TimestampedEntity {
  name: string;
  description: string | null;
  slug: string;
  image: string | null;
}

export function getTopicUri(topic: Topic) {
  return `/topics/${topic.slug}`;
}

export function getTopicImage(topic?: Topic|null) {
  return topic?.image ?? require('@/assets/img/defaults/default-topic-image.png');
}

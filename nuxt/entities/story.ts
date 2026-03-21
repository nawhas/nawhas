export interface Story {
  id: string;
  slug: string;
  title: string;
  body: string | null;
  excerpt: string | null;
  heroImageUrl: string | null;
  displayDate: string | null;
  publishedAt?: string | null;
  createdAt?: string | null;
  updatedAt?: string | null;
}

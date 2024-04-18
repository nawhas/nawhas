import type { NuxtAxiosInstance } from '@nuxtjs/axios';
import { DraftLyrics, LyricsDocument } from '@/entities/lyrics';

export interface StoreDraftLyricsPayload {
  track_id: string;
  document: LyricsDocument
}

export interface UpdateDraftLyricsPayload {
  document: LyricsDocument
}

export class DraftLyricsApi {
  constructor(
    private axios: NuxtAxiosInstance,
  ) {}

  async index(trackId: string): Promise<DraftLyrics> {
    return await this.axios.$get<DraftLyrics>(`v1/drafts/lyrics?track_id=${trackId}`);
  }

  async lock(draftLyricsId: string): Promise<void> {
    return await this.axios.$post<void>(`v1/drafts/lyrics/${draftLyricsId}/lock`);
  }

  async unlock(draftLyricsId: string): Promise<void> {
    return await this.axios.$post<void>(`v1/drafts/lyrics/${draftLyricsId}/unlock`);
  }

  async store(payload: StoreDraftLyricsPayload): Promise<DraftLyrics> {
    return await this.axios.$post<DraftLyrics>('v1/drafts/lyrics', payload);
  }

  async show(draftLyricsId: string): Promise<DraftLyrics> {
    return await this.axios.$get<DraftLyrics>(`v1/drafts/lyrics/${draftLyricsId}`);
  }

  async update(draftLyricsId: string, payload: UpdateDraftLyricsPayload): Promise<DraftLyrics> {
    return await this.axios.$patch<DraftLyrics>(`v1/drafts/lyrics/${draftLyricsId}`, payload);
  }

  async delete(draftLyricsId: string): Promise<void> {
    return await this.axios.$delete<void>(`v1/drafts/lyrics/${draftLyricsId}`);
  }

  async publish(draftLyricsId: string) {
    return await this.axios.$post<void>(`v1/drafts/lyrics/${draftLyricsId}/publish`);
  }
}

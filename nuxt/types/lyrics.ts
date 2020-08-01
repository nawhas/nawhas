export interface Line {
  text: string;
  repeat: number;
}

export interface LineGroup {
  timestamp: number | null;
  lines: Array<Line>;
  type?: string | null;
}

export type LyricsData = Array<LineGroup>;

export interface LyricsMetadata {
  timestamps: boolean;
}

export interface Lyrics {
  meta: LyricsMetadata;
  data: LyricsData;
}

export interface LyricsModel {
  id: string;
  content: string;
  format: 1 | 2;
}

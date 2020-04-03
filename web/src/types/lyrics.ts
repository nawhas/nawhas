export interface Line {
  text: string;
  repeat: number;
}

export interface LineGroup {
  timestamp: number | null;
  lines: Array<Line>;
}

export type LyricsData = Array<LineGroup>;

export interface LyricsMetadata {
  timestamps: boolean;
}

export interface Lyrics {
  meta: LyricsMetadata;
  data: LyricsData;
}

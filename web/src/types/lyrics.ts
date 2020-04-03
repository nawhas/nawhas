export interface Line {
  text: string;
  repeat: number;
}

export interface LineGroup {
  timestamp: number|null;
  lines: Array<Line>;
}

export type Lyrics = Array<LineGroup>;

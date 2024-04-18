import { PersistedEntity, TimestampedEntity } from '@/entities/common';

export namespace Documents {
  export namespace JsonV1 {
    export interface Line {
      text: string;
      repeat: number;
    }

    export enum LineGroupType {
      Normal = 'normal',
      Spacer = 'spacer'
    }

    export interface LineGroup {
      timestamp: number | null;
      lines: Array<Line>;
      type?: LineGroupType | null;
    }

    export type LyricsData = Array<LineGroup>;

    export interface LyricsMetadata {
      timestamps: boolean;
    }

    export interface Document {
      meta: LyricsMetadata;
      data: LyricsData;
    }
  }
}

export enum Format {
  PlainText = 1,
  JsonV1 = 2,
}

export interface LyricsDocument {
  content: string;
  format: Format;
}

export interface Lyrics extends LyricsDocument, PersistedEntity, TimestampedEntity {
  trackId: string;
}

import { PlayerState } from '@/store/player';
import { Documents } from '@/api/lyrics';
import Lyrics = Documents.JsonV1.Document;

export default class LyricsHighlighter {
  constructor(
    private state: PlayerState,
    private lyrics: Lyrics,
  ) {}

  get current(): number|null {
    if (this.state.current === null) {
      return null;
    }

    let current = 0;
    const { seek } = this.state;

    for (const [groupId, group] of this.lyrics.data.entries()) {
      if (group.timestamp !== null) {
        if (group.timestamp < seek) {
          current = groupId;
        } else {
          break;
        }
      }
    }

    return current;
  }
}

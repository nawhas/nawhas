import { PlayerState } from '@/store/modules/player';
import { LyricsData } from '@/types/lyrics';

export default class LyricsHighlighter {
  constructor(
    private state: PlayerState,
    private lyrics: LyricsData,
  ) {}

  get current(): number|null {
    if (this.state.current === null) {
      return null;
    }

    let current = 0;
    const { seek } = this.state;

    for (const [groupId, group] of this.lyrics.entries()) {
      if (group.timestamp && group.timestamp < seek) {
        current = groupId;
      } else {
        break;
      }
    }

    return current;
  }
}
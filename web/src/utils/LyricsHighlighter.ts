import { PlayerState } from '@/store/modules/player';
import { Lyrics } from '@/types/lyrics';

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

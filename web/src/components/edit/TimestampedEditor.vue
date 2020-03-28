<template>
  <v-sheet :class="classes">
    <div class="group" v-for="(group, groupId) in lyrics" :key="groupId">
      <div class="group__timestamp">{{ formatTimestamp(group.timestamp) }}</div>
      <div class="group__lines">
        <div class="line" v-for="(line, lineId) in group.lines" :key="lineId">
          <editable-text class="line__text"
                         v-model="line.text"
                         autocapitalize="off"
                         autocomplete="off"
                         aria-autocomplete="none"
                         spellcheck="false"
                         :ref="`group-${groupId}-line-${lineId}`"
                         @keydown="onKeyDown($event, group, line, { group: groupId, line: lineId })"
                         @focus="onFocus($event, group, line, { group: groupId, line: lineId })"
                         @blur="onBlur($event, group, line, { group: groupId, line: lineId })"
                         @input="change"
          />
          <div class="line__actions">
            <repeat-line v-model="line.repeat" @change="onRepeatChange" />
          </div>
        </div>
      </div>
    </div>
  </v-sheet>
</template>

<script lang="ts">
import { Component, Model, Vue } from 'vue-property-decorator';
import { position } from 'caret-pos';
import RepeatLine from '@/components/edit/RepeatLine.vue';
import * as moment from 'moment';
import EditableText from '@/components/edit/EditableText.vue';
import { clone } from '@/utils/clone';

interface LineCoordinates {
  group: number;
  line: number;
}

interface Line {
  text: string;
  repeat: number;
}

interface LineGroup {
  timestamp: number;
  lines: Array<Line>;
}

type Lyrics = Array<LineGroup>;

@Component({
  components: {
    RepeatLine,
    EditableText,
  },
})
export default class TimestampedEditor extends Vue {
  @Model('change', { type: Array }) readonly model!: Lyrics;
  private lyrics: Lyrics = [];
  private focused = false;
  private selected: LineCoordinates|null = null;

  get classes() {
    return {
      editor: true,
      'editor--dark': this.$vuetify.theme.dark,
      'editor--focused': this.focused,
    };
  }

  mounted() {
    this.lyrics = clone(this.model);
  }

  change() {
    this.$emit('change', clone(this.lyrics));
  }

  /**
   * Adds a new group to lyrics
   */
  addNewGroup(at, lines: Array<Line>|null = null) {
    this.lyrics.splice(at + 1, 0, {
      timestamp: this.$store.state.player.seek,
      lines: lines || [
        { text: '', repeat: 0 },
      ],
    });
    this.focus({ group: at + 1, line: 0 });
    this.change();
  }

  onKeyDown(e: KeyboardEvent, group: LineGroup, line: Line, coordinates: LineCoordinates) {
    switch (e.key) {
      case 'Down':
      case 'ArrowDown':
        e.preventDefault();
        this.goToNextLine(coordinates);
        break;
      case 'Up':
      case 'ArrowUp':
        e.preventDefault();
        this.goToPreviousLine(coordinates);
        break;
      case 'Enter':
        e.preventDefault();
        this.onEnter(group, line, coordinates);
        break;
      case 'Backspace':
        this.onBackspace(group, line, coordinates, e);
        break;
      case 'Delete':
        // TODO
        break;
      default:
        this.change();
    }
  }

  /**
   * When the user presses enter
   * Either add a new line
   * Or add a new group
   */
  onEnter(group: LineGroup, line: Line, coordinates: LineCoordinates) {
    // If this is the only line in the group
    // and the line is empty, do nothing.
    if (group.lines.length === 1 && line.text.length === 0) {
      return;
    }

    const cursor = this.getCursorPosition(coordinates);

    // If it's an empty line, add a new group and delete this line.
    if (line.text.length === 0) {
      let linesToMove: Array<Line>|null = null;
      // If there are other lines in this group, move them to the new group.
      if (group.lines.length > coordinates.line + 1) {
        linesToMove = group.lines.splice(coordinates.line + 1);
      }

      this.onBackspace(group, line, coordinates);
      this.addNewGroup(coordinates.group, linesToMove);
      return;
    }

    // If the cursor is at the start of the line
    if (cursor === 0 && coordinates.line > 0) {
      const linesToMove: Array<Line>|null = group.lines.splice(coordinates.line);
      this.addNewGroup(coordinates.group, linesToMove);

      if (group.lines.length === 0) {
        this.lyrics.splice(coordinates.group, 1);
        this.change();
        // TODO - solve this when updating component to use change events properly.
      }

      return;
    }

    // Add a new line. Split the line at the cursor location.
    let newLine = '';
    if (line.text.length !== cursor) {
      newLine = line.text.substring(cursor).trimEnd();
      // eslint-disable-next-line
      line.text = line.text.substring(0, cursor);
    }
    group.lines.splice(coordinates.line + 1, 0, { text: newLine, repeat: 0 });

    this.change();
    this.focus({ group: coordinates.group, line: coordinates.line + 1 });
  }

  /**
   * Either remove a line
   * Or remove a group
   */
  onBackspace(group: LineGroup, line: Line, coordinates: LineCoordinates, e: KeyboardEvent|null = null) {
    const cursor = this.getCursorPosition(coordinates);

    // If the line has any text, and the cursor is not at the first position,
    // don't do anything. Let the native functionality work.
    if (line.text.length !== 0 && cursor !== 0) {
      return;
    }

    if (e) e.preventDefault();

    let newCursorPosition: number|undefined;

    // If the cursor is at the start of the line,
    // and there is some text in this line,
    // merge this line with the previous line.
    if (cursor === 0 && line.text.length !== 0) {
      if (coordinates.line !== 0) {
        // If we have a previous line in the same group,
        // merge with that one.
        const previousLine = group.lines[coordinates.line - 1];
        newCursorPosition = previousLine.text.length;
        previousLine.text += line.text;
      } else if (coordinates.group !== 0) {
        // If we do not have a previous line in the same group
        // Merge the text to the last line of the previous group
        const previousGroup = this.lyrics[coordinates.group - 1];
        const lastLineOfPrevGroup = previousGroup.lines[previousGroup.lines.length - 1];
        newCursorPosition = lastLineOfPrevGroup.text.length;
        lastLineOfPrevGroup.text += line.text;
      }
      this.change();
    }

    // If this is the last group and the last line, don't delete it.
    if (this.lyrics.length === 1 && group.lines.length === 1) {
      return;
    }

    // Delete the line.
    group.lines.splice(coordinates.line, 1);

    // If the line is the last line in the group,
    // and we delete it, delete the group as well.
    if (group.lines.length === 0) {
      // Delete group.
      this.lyrics.splice(coordinates.group, 1);
    }
    this.change();
    this.goToPreviousLine(coordinates, newCursorPosition);
  }

  /**
   * Format the timestamp to Minutes and Seconds
   */
  formatTimestamp(timestamp) {
    return moment.utc(moment.duration(timestamp, 'seconds').asMilliseconds()).format('m:ss');
  }

  /**
   * Go to either the next line
   * Or to the next group
   */
  goToNextLine(current: LineCoordinates) {
    let newLineId = current.line;
    let newGroupId = current.group;
    const cursor = this.getCursorPosition(current);

    const group = this.lyrics[current.group];
    if (group.lines.length > current.line + 1) {
      newLineId = current.line + 1;
    } else if (this.lyrics.length > current.group + 1) {
      newGroupId = current.group + 1;
      newLineId = 0;
    } else {
      return;
    }

    this.focus({ group: newGroupId, line: newLineId }, cursor);
  }

  /**
   * Either go to the previous line
   * Or to the previous group
   */
  goToPreviousLine(current: LineCoordinates, cursor: number|undefined = undefined) {
    let newLineId = current.line;
    let newGroupId = current.group;

    if (current.line !== 0) {
      newLineId = current.line - 1;
    } else if (current.group !== 0) {
      newGroupId = current.group - 1;
      const prevGroup = this.lyrics[newGroupId];
      newLineId = prevGroup.lines.length - 1;
    }

    const newCursorPosition = cursor || this.getCursorPosition({ group: current.group, line: current.line });

    this.focus({ group: newGroupId, line: newLineId }, newCursorPosition);
  }

  onRepeatChange() {
    this.change();
  }

  onFocus(e: FocusEvent, group: LineGroup, line: Line, coordinates: LineCoordinates) {
    this.focused = true;
    this.selected = coordinates;
  }

  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  onBlur(e: FocusEvent, group: LineGroup, line: Line, coordinates: LineCoordinates) {
    this.focused = false;
    this.selected = null;
  }

  focus(coordinates: LineCoordinates, cursor: number|undefined = undefined) {
    this.$nextTick(() => {
      try {
        const input = this.getLineInput(coordinates);
        input.focus();
        if (typeof cursor !== 'undefined') {
          const cursorPosition = (input.innerText.length < cursor) ? input.innerText.length : cursor;
          position(input, cursorPosition);
        }
      } catch (e) {
        // console.error(e);
        // Do nothing.
      }
    });
  }

  getCursorPosition(coordinates: LineCoordinates): number {
    return position(this.getLineInput(coordinates)).pos;
  }

  /**
   * Get the Reference from the DOM
   */
  getLineInput(coordinates: LineCoordinates): HTMLDivElement {
    const ref = (this.$refs[this.getLineInputKey(coordinates)] as Array<Vue>);

    if (!ref.length) {
      throw new ReferenceError(`Line at (${coordinates.group}, ${coordinates.line}) does not exist.`);
    }
    return (ref[0].$el as HTMLDivElement);
  }

  getLineInputKey({ group, line }: LineCoordinates): string {
    return `group-${group}-line-${line}`;
  }
}
</script>

<style lang="scss" scoped>
@import "../../styles/theme";

.editor {
  padding: 12px;
  font-family: 'Roboto Slab', 'serif';
  font-size: 1.15rem;
  border: 1px solid rgba(0,0,0,0.3);
  border-collapse: collapse;
  box-sizing: border-box;
  @include transition(border);

  &--focused {
    border: 1px solid $primary;
    box-shadow: 0 0 0 1px $primary;
  }
}
.group {
  display: flex;
  margin-bottom: 12px;
}
.group__timestamp {
  font-size: 0.95rem;
  width: 30px;
  opacity: 0.6;
  padding-top: 6px;
}

.group__lines {
  flex-grow: 1;
}

.line {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.line__text {
  flex-grow: 1;
  padding: 6px;
}
.line__actions {
  flex-shrink: 1;
  font-size: 0.95rem;
}

.line__text {
  font-size: 1rem;
  outline: none;
  margin: 0 12px 0 8px;
  white-space: pre;
}
</style>

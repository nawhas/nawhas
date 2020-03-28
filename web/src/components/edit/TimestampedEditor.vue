<template>
  <v-sheet :class="classes">
    <div class="editor__header">
      <div class="header__icon">
        <v-icon>speaker_notes</v-icon>
      </div>
      <div class="header__title">Write-Up</div>
      <div class="header__actions">
        <v-btn :disabled="!canUndo" icon @click="undo"><v-icon>undo</v-icon></v-btn>
        <v-btn :disabled="!canRedo" icon @click="redo"><v-icon>redo</v-icon></v-btn>
      </div>
    </div>
    <div class="editor__content">
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
    </div>
  </v-sheet>
</template>

<script lang="ts">
import { Component, Model, Vue } from 'vue-property-decorator';
import { position } from 'caret-pos';
import RepeatLine from '@/components/edit/RepeatLine.vue';
import * as moment from 'moment';
import EditableText from '@/components/edit/EditableText.vue';
import StateHistory from '@/utils/StateHistory';
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
  private history: StateHistory<Lyrics> = new StateHistory([]);
  private changeTimeout: number|undefined;

  get classes() {
    return {
      editor: true,
      'editor--dark': this.$vuetify.theme.dark,
      'editor--focused': this.focused,
    };
  }

  get canUndo() {
    return this.history.previous.length > 0;
  }

  get canRedo() {
    return this.history.future.length > 0;
  }

  mounted() {
    this.lyrics = clone(this.model);
    this.history = new StateHistory(this.lyrics);
  }

  change() {
    if (this.changeTimeout) {
      window.clearTimeout(this.changeTimeout);
    }

    this.changeTimeout = window.setTimeout(() => {
      this.$emit('change', clone(this.lyrics));
      this.history.commit(this.lyrics);
    }, 200);
  }

  /**
   * Adds a new group to lyrics
   */
  addNewGroup(at, lines: Array<Line>|null = null) {
    let timestamp = 0;
    if (this.$store.state.player.current) {
      timestamp = this.$store.state.player.seek;
    } else if (this.lyrics.length > at + 1) {
      timestamp = this.lyrics[at].timestamp;
    } else {
      timestamp = 0;
    }
    this.lyrics.splice(at + 1, 0, {
      timestamp,
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
        this.onDelete(group, line, coordinates, e);
        break;
      case 'z':
      case 'Z':
        if (e.metaKey || e.ctrlKey) {
          e.preventDefault();
          if (e.shiftKey) {
            this.redo();
          } else {
            this.undo();
          }
        }
        break;
      default:
        // this.change();
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
      if (coordinates.group === 0 && coordinates.line === 0) {
        return;
      }

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
    this.deleteLine(coordinates);
    this.goToPreviousLine(coordinates, newCursorPosition);
  }

  onDelete(group: LineGroup, line: Line, coordinates: LineCoordinates, e: KeyboardEvent) {
    const cursor = this.getCursorPosition(coordinates);

    // If the cursor is not at the end of the line,
    // don't do anything. Let the native functionality work.
    if (cursor !== line.text.length) {
      return;
    }

    // Get the next line
    const nextLineCoordinates = this.getNextLineCoordinates(coordinates);
    const nextLine = this.getLine(nextLineCoordinates);
    if (nextLine === null) {
      return;
    }

    // Disable native delete functionality.
    e.preventDefault();

    // Append the next line's content to this line.
    line.text += nextLine.text;

    // Delete the next line
    this.deleteLine(nextLineCoordinates);

    // Restore cursor position.
    this.focus(coordinates, cursor);
  }

  deleteLine(coordinates: LineCoordinates) {
    const group = this.getGroup(coordinates);

    if (group === null) {
      return;
    }

    group.lines.splice(coordinates.line, 1);

    // If the line is the last line in the group,
    // and we delete it, delete the group as well.
    if (group.lines.length === 0) {
      // Delete group.
      this.deleteGroup(coordinates);
    }

    this.change();
  }

  deleteGroup({ group }: LineCoordinates) {
    this.lyrics.splice(group, 1);
    this.change();
  }

  undo() {
    if (!this.history.canUndo()) {
      return;
    }

    const previous = this.history.undo();

    if (this.changeTimeout) {
      window.clearTimeout(this.changeTimeout);
    }

    this.lyrics = clone(previous);
    this.$emit('change', clone(this.lyrics));
  }

  redo() {
    if (!this.history.canRedo()) {
      return;
    }

    const next = this.history.redo();

    if (this.changeTimeout) {
      window.clearTimeout(this.changeTimeout);
    }

    this.lyrics = clone(next);
    this.$emit('change', clone(this.lyrics));
  }

  /**
   * Format the timestamp to Minutes and Seconds
   */
  formatTimestamp(timestamp) {
    return moment.utc(moment.duration(timestamp, 'seconds').asMilliseconds()).format('m:ss');
  }

  getNextLineCoordinates(current: LineCoordinates): LineCoordinates {
    const next = { ...current };
    const group = this.lyrics[current.group];
    if (group.lines.length > current.line + 1) {
      next.line = current.line + 1;
    } else if (this.lyrics.length > current.group + 1) {
      next.group = current.group + 1;
      next.line = 0;
    }

    return next;
  }

  getPreviousLineCoordinates(current: LineCoordinates): LineCoordinates {
    const previous = { ...current };

    if (current.line !== 0) {
      previous.line = current.line - 1;
    } else if (current.group !== 0) {
      previous.group = current.group - 1;
      previous.line = this.lyrics[previous.group].lines.length - 1;
    }

    return previous;
  }

  /**
   * Go to either the next line
   * Or to the next group
   */
  goToNextLine(current: LineCoordinates) {
    const cursor = this.getCursorPosition(current);
    const next = this.getNextLineCoordinates(current);

    this.focus(next, cursor);
  }

  /**
   * Either go to the previous line
   * Or to the previous group
   */
  goToPreviousLine(current: LineCoordinates, cursor: number|undefined = undefined) {
    const previous = this.getPreviousLineCoordinates(current);

    const newCursorPosition = cursor || this.getCursorPosition(current);

    this.focus(previous, newCursorPosition);
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

  getLine(coordinates: LineCoordinates): Line|null {
    return this.getGroup(coordinates)?.lines[coordinates.line] || null;
  }

  getGroup(coordinates: LineCoordinates): LineGroup|null {
    return this.lyrics[coordinates.group];
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
  border: 1px solid rgba(0,0,0,0.3);
  border-collapse: collapse;
  box-sizing: border-box;
  @include transition(border);

  &--focused {
    border: 1px solid $primary;
    box-shadow: 0 0 0 1px $primary;
  }
}


.editor--dark {
  border-color: #545454;

  .editor__header {
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
}

.editor__header {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  border-bottom: 1px solid rgba(0,0,0,0.08);

  .header__icon {
    margin-right: 12px;
    opacity: 0.8;
  }
  .header__title {
    font-size: 1rem;
    font-weight: 500;
    flex: 1;
  }
}

.editor__content {
  padding: 12px;
}

.group {
  font-family: 'Roboto Slab', 'serif';
  font-size: 1.15rem;
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
  white-space: pre-wrap;
}
</style>

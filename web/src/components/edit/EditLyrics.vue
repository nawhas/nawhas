<template>
  <v-card class="editor" flat outlined>
    <table>
      <tbody>
        <!-- Group -->
        <tr v-for="(group, groupId) in lyrics" :key="groupId">
          <!-- Timestamp -->
          <td class="timestamp">{{ formatTimestamp(group.timestamp) }}</td>
          <td class="content">
            <table class="lines">
              <tbody>
                <tr v-for="(line, lineId) in group.lines" :key="lineId">
                  <td class="line-text">
                    <!-- Line Text Field -->
                    <editable-text class="line__text"
                                   v-model="line.text"
                                   autocapitalize="off"
                                   autocomplete="off"
                                   aria-autocomplete="none"
                                   spellcheck="false"
                                   :ref="`group-${groupId}-line-${lineId}`"
                                   @keydown="onKeyDown($event, group, line, { group: groupId, line: lineId })"
                    ></editable-text>
                  </td>
                  <td class="repeat">
                    <repeat-line v-model="line.repeat" />
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </v-card>
</template>

<script lang="ts">
import { Component, Model, Vue } from 'vue-property-decorator';
import { position } from 'caret-pos';
import RepeatLine from '@/components/edit/RepeatLine.vue';
import * as moment from 'moment';
import EditableText from '@/components/edit/EditableText.vue';

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
export default class EditLyrics extends Vue {
  @Model('change', { type: Array }) readonly lyrics!: Lyrics;

  /**
   * Adds a new group to lyrics
   */
  addNewGroup(at, line: Line|null = null) {
    const updated = [...this.lyrics];
    updated.splice(at + 1, 0, {
      timestamp: this.$store.state.player.seek,
      lines: [
        line || { text: '', repeat: 0 },
      ],
    });
    this.$emit('change', updated);
    this.focus({ group: at + 1, line: 0 });
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
        e.preventDefault();
        this.onBackspace(group, line, coordinates);
        break;
      case 'Delete':
        // TODO
        break;
      default:
        // Do nothing.
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
      this.onBackspace(group, line, coordinates);
      this.addNewGroup(coordinates.group);
      return;
    }

    // Add a new line. Split the line at the cursor location.
    let newLine = '';
    if (line.text.length !== cursor) {
      newLine = line.text.substring(cursor);
      // eslint-disable-next-line
      line.text = line.text.substring(0, cursor);
    }
    group.lines.splice(coordinates.line + 1, 0, { text: newLine, repeat: 0 });

    this.focus({ group: coordinates.group, line: coordinates.line + 1 });
  }

  /**
   * Either remove a line
   * Or remove a group
   */
  onBackspace(group: LineGroup, line: Line, coordinates: LineCoordinates) {
    const cursor = this.getCursorPosition(coordinates);

    // If the line has any text, and the cursor is not at the first position,
    // don't do anything. Let the native functionality work.
    if (line.text.length !== 0 && cursor !== 0) {
      return;
    }

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
.editor {
  padding: 12px;
}
table {
  width: 100%;
  font-family: 'Roboto Slab', 'serif';
  font-size: 1.15rem;
}
.timestamp {
  font-size: 0.95rem;
  width: 30px;
  vertical-align: top;
  opacity: 0.6;
  height: 40px;
  line-height: 40px;
}

.repeat {
  width: 90px;
  font-size: 0.95rem;
}

.line__text {
  font-size: 1rem;
  outline: none;
  margin: 0 12px 0 8px;
}
</style>

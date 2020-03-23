<template>
  <v-card class="editor" flat outlined>
    <table>
      <tbody>
        <!-- Group -->
        <tr v-for="(group, groupId) in value" :key="groupId">
          <!-- Timestamp -->
          <td class="timestamp">{{ formatTimestamp(group.timestamp) }}</td>
          <td class="content">
            <table class="lines">
              <tbody>
                <tr v-for="(line, lineId) in group.lines" :key="lineId">
                  <td class="line-text">
                    <!-- Line Text Field -->
                    <v-text-field
                      :ref="`group-${groupId}-line-${lineId}`"
                      dense
                      @keyup.enter="onEnter($event, group, groupId, line, lineId)"
                      @keyup.delete="onDelete($event, group, groupId, line, lineId)"
                      @keydown.up="goToPreviousLine(groupId, lineId)"
                      @keydown.down="goToNextLine(groupId, lineId)"
                      v-model="line.text"
                      aria-autocomplete="none"
                      autocomplete="off"
                      placeholder="Add a line"
                      solo flat hide-details
                    ></v-text-field>
                  </td>
                  <td class="repeat">
                    <repeat-line v-model="line.repeat" />
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
          <td class="type" v-if="false">
            <v-select hide-details :items="types" v-model="group.type" solo flat label="Type"></v-select>
          </td>
        </tr>
      </tbody>
    </table>
  </v-card>
</template>

<script lang="ts">
import { Component, Model, Vue } from 'vue-property-decorator';
import RepeatLine from '@/components/edit/RepeatLine.vue';
import * as moment from 'moment';

interface Line {
  text: string;
  repeat: number;
}

interface LineGroup {
  timestamp: number;
  type: string;
  lines: Array<Line>;
}

type Lyrics = Array<LineGroup>;

@Component({
  components: {
    RepeatLine,
  },
})
export default class EditLyrics extends Vue {
  private types: Array<string> = [
    'Normal',
    'Chorus',
    'Verse',
    'Break',
  ];

  @Model('change', { type: Array }) readonly value!: Lyrics;

  /**
   * Adds a new group to lyrics
   */
  addNewGroup(at) {
    const updated = [...this.value];
    updated.splice(at + 1, 0, {
      timestamp: this.$store.state.player.seek,
      type: this.types[0],
      lines: [
        { text: '', repeat: 0 },
      ],
    });
    this.updateModel(updated);
    this.$nextTick(() => this.getRef(at + 1, 0).focus());
  }

  /**
   * When the user presses enter
   * Either add a new line
   * Or add a new group
   */
  onEnter(e, group, groupId, line, lineId) {
    // If this is the only line in the group
    // and the line is empty, do nothing.
    if (group.lines.length === 1 && line.text.length === 0) {
      return;
    }

    // If it's an empty line, add a new group and delete this line.
    if (line.text.length === 0) {
      this.onDelete(e, group, groupId, line, lineId);
      this.addNewGroup(groupId);
      return;
    }

    const cursor = e.target.selectionStart;

    // Add a new line. Split the line at the cursor location.
    let newLine = '';
    if (line.text.length !== cursor) {
      newLine = line.text.substring(cursor);
      // eslint-disable-next-line
      line.text = line.text.substring(0, cursor);
    }
    group.lines.splice(lineId + 1, 0, { text: newLine, repeat: 0 });
    this.$nextTick(() => this.getRef(groupId, lineId + 1).focus());
  }

  /**
   * Either remove a line
   * Or remove a group
   */
  onDelete(e, group, groupId, line, lineId) {
    const cursor = e.target.selectionStart;

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
      if (lineId !== 0) {
        // If we have a previous line in the same group,
        // merge with that one.
        const previousLine = group.lines[lineId - 1];
        newCursorPosition = previousLine.text.length;
        previousLine.text += line.text;
      } else if (groupId !== 0) {
        // If we do not have a previous line in the same group
        // Merge the text to the last line of the previous group
        const previousGroup = this.value[groupId - 1];
        const lastLineOfPrevGroup = previousGroup.lines[previousGroup.lines.length - 1];
        newCursorPosition = lastLineOfPrevGroup.text.length;
        lastLineOfPrevGroup.text += line.text;
      }
    }

    // If this is the last group and the last line, don't delete it.
    if (this.value.length === 1 && group.lines.length === 1) {
      return;
    }

    // Delete the line.
    group.lines.splice(lineId, 1);

    // If the line is the last line in the group,
    // and we delete it, delete the group as well.
    if (group.lines.length === 0) {
      // Delete group.
      this.value.splice(groupId, 1);
    }
    this.goToPreviousLine(groupId, lineId, newCursorPosition);
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
  goToNextLine(groupId, lineId) {
    const group = this.value[groupId];
    if (group.lines.length > lineId + 1) {
      this.getRef(groupId, lineId + 1).focus();
      return;
    }

    if (this.value.length > groupId + 1) {
      this.getRef(groupId + 1, 0).focus();
    }
  }

  /**
   * Either go to the previous line
   * Or to the previous group
   */
  goToPreviousLine(groupId, lineId, cursor: number|undefined) {
    let newLineId = lineId;
    let newGroupId = groupId;

    if (lineId !== 0) {
      newLineId = lineId - 1;
    } else if (groupId !== 0) {
      newGroupId = groupId - 1;
      const prevGroup = this.value[newGroupId];
      newLineId = prevGroup.lines.length - 1;
    }

    const input = this.getRef(newGroupId, newLineId);

    if (typeof cursor !== 'undefined') {
      input.$refs.input.setSelectionRange(cursor, cursor);
      // console.log(input.$refs.input.selectionStart);
    }
    input.focus();
  }

  /**
   * Get the Reference from the DOM
   */
  getRef(groupId, lineId): any {
    const ref = `group-${groupId}-line-${lineId}`;

    return (this.$refs[ref][0] as any);
  }

  updateModel(value: Lyrics) {
    this.$emit('change', value);
  }
}
</script>

<style lang="scss" scoped>
.editor {
  padding: 12px;
}
table {
  width: 100%;
  font-family: 'Roboto Slab';
  font-size: 1.15rem;
}
.timestamp {
  font-size: 0.95rem;
  width: 30px;
  vertical-align: top;
  padding-top: 10px;
  opacity: 0.6;
}

.repeat {
  width: 90px;
  font-size: 0.95rem;
}
</style>

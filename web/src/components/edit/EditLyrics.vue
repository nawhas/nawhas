<template>
  <v-card class="editor" flat outlined>
    <table>
      <tbody>
        <!-- Group -->
        <tr v-for="(group, groupId) in lyrics" :key="groupId">
          <!-- Timestamp -->
          <td @dblclick="setTimestamp(groupId)" class="timestamp">{{ formatTimestamp(group.timestamp) }}</td>
          <td class="content">
            <table class="lines">
              <tbody>
                <tr v-for="(line, lineId) in group.lines" :key="lineId">
                  <td class="line-text">
                    <!-- Line Text Field -->
                    <v-text-field
                      :ref="`group-${groupId}-line-${lineId}`"
                      dense
                      @keyup.enter="onEnter(group, groupId, line, lineId)"
                      @keyup.delete="onDelete(group, groupId, line, lineId)"
                      @keyup.up="goToPreviousLine(groupId, lineId)"
                      @keyup.down="goToNextLine(groupId, lineId)"
                      v-model="line.text"
                      aria-autocomplete="none"
                      autocomplete="off"
                      placeholder="Write the writeup here."
                      solo flat hide-details
                    ></v-text-field>
                  </td>
                  <td class="repeat">
                    <repeat-line />
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
import { Component, Vue } from 'vue-property-decorator';
import RepeatLine from '@/components/edit/RepeatLine.vue';
import * as moment from 'moment';

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

  private lyrics: Array<any> = [
    {
      timestamp: 0,
      type: this.types[0],
      lines: [
        { text: '', repeat: 0 },
      ],
    },
  ];

  /**
   * Adds a new group to lyrics
   */
  addNewGroup(at) {
    this.lyrics.splice(at + 1, 0, {
      timestamp: 0,
      type: this.types[0],
      lines: [
        { text: '', repeat: 0 },
      ],
    });
    this.setTimestamp(this.lyrics.length - 1);
    this.$nextTick(() => this.getRef(at + 1, 0).focus());
  }

  /**
   * When the user presses enter
   * Eitehr add a new line
   * Or add a new group
   */
  onEnter(group, groupId, line, lineId) {
    // If this is the only line in the group
    // and the line is empty, do nothing.
    if (group.lines.length === 1 && line.text.length === 0) {
      return;
    }

    if (line.text.length === 0) {
      this.onDelete(group, groupId, line, lineId);
      this.addNewGroup(groupId);
    } else {
      group.lines.splice(lineId + 1, 0, { text: '', repeat: 0 });
      this.$nextTick(() => this.getRef(groupId, lineId + 1).focus());
    }
  }

  /**
   * Either remove a line
   * Or remove a group
   */
  onDelete(group, groupId, line, lineId) {
    // If the line has any text, don't do anything.
    // Let the native functionality work.
    if (line.text.length !== 0) {
      return;
    }

    // If this is the last group and the last line, don't delete it.
    if (this.lyrics.length === 1 && group.lines.length === 1) {
      return;
    }

    // Delete the line.
    group.lines.splice(lineId, 1);

    // If the line is the last line in the group,
    // and we delete it, delete the group as well.
    if (group.lines.length === 0) {
      // Delete group.
      this.lyrics.splice(groupId, 1);
    }
    this.goToPreviousLine(groupId, lineId);
  }

  /**
   * Add 1 to the line repeat
   */
  incrementRepeatForLine(groupId, lineId) {
    if (this.lyrics[groupId].lines[lineId].repeat === 0) {
      this.lyrics[groupId].lines[lineId].repeat = 2;
      return true;
    }
    this.lyrics[groupId].lines[lineId].repeat++;
    return true;
  }

  /**
   * Remove 1 from the line repeat
   */
  decrementRepeatForLine(groupId, lineId) {
    if (this.lyrics[groupId].lines[lineId].repeat === 2) {
      this.lyrics[groupId].lines[lineId].repeat = 0;
      return true;
    }
    if (this.lyrics[groupId].lines[lineId].repeat === 0) {
      return false;
    }
    this.lyrics[groupId].lines[lineId].repeat--;
    return true;
  }

  /**
   * Format the timestamp to Minutes and Seconds
   */
  formatTimestamp(timestamp) {
    return moment.utc(moment.duration(timestamp, 'seconds').asMilliseconds()).format('m:ss');
  }

  /**
   * Set's the timestamp of a group from the audio player
   */
  setTimestamp(index) {
    this.lyrics[index].timestamp = this.$store.state.player.seek;
  }

  /**
   * Go to either the next line
   * Or to the next group
   */
  goToNextLine(groupId, lineId) {
    const group = this.lyrics[groupId];
    if (group.lines.length > lineId + 1) {
      this.getRef(groupId, lineId + 1).focus();
      return;
    }

    if (this.lyrics.length > groupId + 1) {
      this.getRef(groupId + 1, 0).focus();
    }
  }

  /**
   * Either go to the previous line
   * Or to the previous group
   */
  goToPreviousLine(groupId, lineId) {
    if (lineId !== 0) {
      this.getRef(groupId, lineId - 1).focus();
      return;
    }

    if (groupId !== 0) {
      const prevGroupId = groupId - 1;
      const prevGroup = this.lyrics[prevGroupId];
      this.getRef(prevGroupId, prevGroup.lines.length - 1).focus();
    }
  }

  /**
   * Get the Reference from the DOM
   */
  getRef(groupId, lineId): HTMLElement {
    const ref = `group-${groupId}-line-${lineId}`;

    return (this.$refs[ref][0] as HTMLElement);
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

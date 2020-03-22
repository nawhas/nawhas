<template>
  <v-card class="editor" flat outlined>
    <table>
      <tbody>
        <tr v-for="(group, groupId) in lyrics" :key="groupId">
          <td @dblclick="setTimestamp(groupId)" class="timestamp">{{ formatTimestamp(group.timestamp) }}</td>
          <td class="content">
            <table class="lines">
              <tbody>
                <tr v-for="(line, lineId) in group.lines" :key="lineId">
                  <td>
                    <v-text-field
                      dense
                      v-on:keyup.enter="onEnter(group, groupId, line, lineId)"
                      v-on:keyup.delete="removeLine(groupId, lineId)"
                      v-model="line.text"
                      placeholder="Please enter text"
                      solo flat hide-details
                    ></v-text-field>
                  </td>
                  <td v-if="false">
                    <v-btn @click="subtract(groupId, lineId)" icon small>
                      <v-icon>remove</v-icon>
                    </v-btn>
                    <span class="repeat-value d-inline-block mx-2">{{ line.repeat }}</span>
                    <v-btn @click="add(groupId, lineId)" icon small>
                      <v-icon>add</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
          <td class="type" v-if="false">
            <v-select hide-details :items="types" v-model="lines.type" solo flat label="Type"></v-select>
          </td>
        </tr>
      </tbody>
    </table>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import * as moment from 'moment';

@Component
export default class EditLyrics extends Vue {
  private lyrics: Array<any> = [
    {
      timestamp: 0,
      // type: this.[0],
      lines: [
        { text: '', repeat: 0 },
      ],
    },
  ];
  private types: Array<string> = [
    'Normal',
    'Chorus',
    'Verse',
    'Break',
  ];

  addNewGroup(at) {
    this.lyrics.splice(at + 1, 0, {
      timestamp: 0,
      type: this.types[0],
      lines: [
        { text: '', repeat: 0 },
      ],
    });
    this.setTimestamp(this.lyrics.length - 1);
  }

  onEnter(group, groupId, line, lineId) {
    if (line.text.length === 0) {
      this.removeLine(groupId, lineId);
      this.addNewGroup(groupId);
    } else {
      group.lines.splice(lineId + 1, 0, { text: '', repeat: 0 });
    }
  }

  removeGroup(groupId) {
    this.lyrics.splice(groupId, 1);
  }

  removeLine(groupId, lineId) {
    if (this.lyrics[groupId].lines.length === 1) {
      this.removeGroup(groupId);
      return true;
    }
    this.lyrics[groupId].lines.splice(lineId, 1);
    return true;
  }

  add(groupId, lineId) {
    if (this.lyrics[groupId].lines[lineId].repeat === 0) {
      this.lyrics[groupId].lines[lineId].repeat = 2;
      return true;
    }
    this.lyrics[groupId].lines[lineId].repeat++;
    return true;
  }

  subtract(groupId, lineId) {
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

  formatTimestamp(timestamp) {
    return moment.utc(moment.duration(timestamp, 'seconds').asMilliseconds()).format('m:ss');
  }

  setTimestamp(index) {
    this.lyrics[index].timestamp = this.$store.state.player.seek;
  }
}
</script>

<style lang="scss" scoped>
.editor {
  padding: 12px 24px;
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
.content {
  // width: 70%;
}

.type {
  // width: 20%;
}

.lines {
}

.repeat-value {
}
</style>

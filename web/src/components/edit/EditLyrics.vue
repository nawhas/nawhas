<template>
  <v-card @dblclick="addLines" class="editor" flat outlined>
    <table>
      <tbody>
        <tr v-for="(lines, index) in lyrics" :key="index">
          <td @dblclick="setTimestamp(index)" class="timestamp">{{ lines.timestamp }}</td>
          <td class="content">
            <table class="lines">
              <tbody>
                <tr v-for="(line, lineIndex) in lines.lines" :key="lineIndex">
                  <td>
                    <v-text-field
                      v-on:keyup.enter="addLine(index)"
                      v-on:keyup.delete="removeLine(index, lineIndex)"
                      v-model="line.text"
                      placeholder="Please enter text"
                      :disabled="lines.edited === true"
                    ></v-text-field>
                  </td>
                  <td>
                    <v-btn @click="subtract(index, lineIndex)" icon small>
                      <v-icon>remove</v-icon>
                    </v-btn>
                    <span class="repeat-value d-inline-block mx-2">{{ line.repeat }}</span>
                    <v-btn @click="add(index, lineIndex)" icon small>
                      <v-icon>add</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
          <td class="type">
            <v-select hide-details :items="types" v-model="lines.type" solo flat label="Type"></v-select>
          </td>
        </tr>
      </tbody>
    </table>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class EditLyrics extends Vue {
  private lyrics: Array<any> = [];
  private types: Array<string> = [
    'Normal',
    'Chorus',
    'Verse',
    'Break',
  ];

  addLines() {
    this.lyrics.push({
      timestamp: '00:00',
      type: this.types[0],
      lines: [
        { text: '', repeat: 0 },
      ],
      edited: false,
    });
    this.setTimestamp(this.lyrics.length - 1);
  }

  addLine(index) {
    this.lyrics[index].lines.push(
      { text: '', repeat: 0 },
    );
  }

  removeLines(index) {
    this.lyrics.splice(index, 1);
  }

  removeLine(linesIndex, lineIndex) {
    if (this.lyrics[linesIndex].lines.length === 1) {
      this.removeLines(linesIndex);
      return true;
    }
    this.lyrics[linesIndex].lines.splice(lineIndex, 1);
    return true;
  }

  add(linesIndex, lineIndex) {
    if (this.lyrics[linesIndex].lines[lineIndex].repeat === 0) {
      this.lyrics[linesIndex].lines[lineIndex].repeat = 2;
      return true;
    }
    this.lyrics[linesIndex].lines[lineIndex].repeat++;
    return true;
  }

  subtract(linesIndex, lineIndex) {
    if (this.lyrics[linesIndex].lines[lineIndex].repeat === 2) {
      this.lyrics[linesIndex].lines[lineIndex].repeat = 0;
      return true;
    }
    if (this.lyrics[linesIndex].lines[lineIndex].repeat === 0) {
      return false;
    }
    this.lyrics[linesIndex].lines[lineIndex].repeat--;
    return true;
  }

  setTimestamp(index) {
    this.lyrics[index].timestamp = this.$store.state.player.seek;
  }
}
</script>

<style lang="scss" scoped>
.editor {
  padding: 24px;
}
table {
  width: 100%;
  font-family: 'Roboto Slab';
  font-size: 1.15rem;
}
.timestamp {
  font-size: 0.95rem;
  width: 10%;
}
.content {
  width: 70%;
}

.type {
  width: 20%;
}

.lines {
}

.repeat-value {
}
</style>

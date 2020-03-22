<template>
  <v-card class="editor" flat outlined>
    <table>
      <tbody>
        <tr v-for="(lines, index) in lyrics" :key="index">
          <td class="timestamp">{{ lines.timestamp }}</td>
          <td class="content">
            <table class="lines">
              <tbody>
                <tr v-for="(line, lineIndex) in lines.lines" :key="lineIndex">
                  <td>
                    <v-text-field v-model="line.text" :disabled="lines.edited === true"></v-text-field>
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
          <td>
            <v-btn icon v-if="!lines.edited" @click="lines.edited = true">
              <v-icon>done</v-icon>
            </v-btn>
            <v-btn icon v-else @click="lines.edited = false">
              <v-icon>create</v-icon>
            </v-btn>
          </td>
        </tr>
      </tbody>
    </table>
    <v-btn @click="addLines">Add Lines</v-btn>
  </v-card>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class EditLyrics extends Vue {
  private lyrics: Array<object> = [];
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
    console.log(this.lyrics);
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
  width: 60%;
}

.type {
  width: 30%;
}

.lines {
}

.repeat-value {
}
</style>

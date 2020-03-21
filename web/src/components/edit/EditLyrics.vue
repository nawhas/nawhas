<template>
  <v-card class="editor" flat outlined>
    <table>
      <tbody>
        <tr>
          <td class="timestamp">
            0:01
          </td>
          <td class="content">
            <table class="lines">
              <tbody>
                <tr>
                  <td>This is some text</td>
                  <td>
                    <v-btn icon small><v-icon>remove</v-icon></v-btn>
                    <span class="repeat-value d-inline-block mx-2">0</span>
                    <v-btn icon small><v-icon>add</v-icon></v-btn>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
          <td class="type">
            <v-select
              hide-details
              :items="types"
              :value="types[0]"
              solo flat
              label="Type"
            ></v-select>
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
  private lyrics = [];
  private types: Array<string> = [
    'Normal',
    'Chorus',
  ];

  add() {
    console.log('add');
  }

  subtract() {
    console.log('Subtracted');
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
}
.content {

}

.type {

}

.lines {

}

.repat-value {

}
</style>

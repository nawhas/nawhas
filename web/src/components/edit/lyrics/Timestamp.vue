<template>
  <div>
    <v-edit-dialog
          ref="dialog"
          class="dialog"
          :return-value.sync="timestamp"
          @save="save"
          @cancel="cancel"
          @open="open"
          @close="close"
          transition="slide-x-transition"
    >
      <div class="timestamp">{{ format(model) }}</div>
      <template v-slot:input>
        <div class="popup">
          <input class="popup__text"
               :style="{ width: `${timestamp.length}ch` }"
               type="tel"
               autofocus="autofocus"
               autocomplete="off"
               v-model="timestamp"
          />
          <v-btn @click="setTimeFromPlayer" icon><v-icon>update</v-icon></v-btn>
        </div>
      </template>
    </v-edit-dialog>
  </div>
</template>

<script lang="ts">
import {
  Component, Model, Watch, Vue,
} from 'vue-property-decorator';
import * as moment from 'moment';
// import EditableText from '@/components/edit/lyrics/EditableText.vue';

@Component({
  // components: { EditableText },
})
export default class Timestamp extends Vue {
  @Model('change', { type: Number }) private readonly model!: string;
  private timestamp = '';
  private menu = false;

  get color() {
    return this.$vuetify.theme.dark ? 'grey darken-3' : 'white';
  }

  mounted() {
    this.timestamp = this.format(this.model);
  }

  @Watch('model')
  onModelChanged(value) {
    this.timestamp = this.format(value);
  }

  /**
   * Format the timestamp to Minutes and Seconds
   */
  format(timestamp) {
    return moment.utc(moment.duration(timestamp, 'seconds').asMilliseconds()).format('m:ss');
  }

  /**
   * Parse the timestamp to a number
   */
  // parse(timestamp) {
  // return moment(timestamp, 'm:ss').asSeconds();
  // }

  setTimeFromPlayer() {
    const timestamp = this.$store.state.player.seek;
    this.timestamp = this.format(timestamp);
  }

  save() {
    const formatted = moment(this.timestamp, 'm:ss').diff(moment().startOf('day'), 'seconds');
    this.$emit('change', formatted);
  }

  cancel() {
    console.log('Cancelled');
  }

  open() {
    console.log('Opened');
    this.timestamp = this.format(this.model);
    console.log(this.$refs.dialog);
  }

  close() {
    console.log('Closed');
  }
}
</script>

<style lang="scss" scoped>
.dialog {
  background-color: red !important;
}
.timestamp {
  cursor: pointer;
}
.popup {
  padding: 4px;
  display: flex;
  align-items: center;
}

.popup__text {
  min-width: 8px;
  margin-right: 4px;
  outline: none;
  font-family: 'Roboto Slab', serif;
}
</style>

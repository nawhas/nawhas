<template>
  <div>
    <v-edit-dialog
          ref="dialog"
          class="dialog"
          @cancel="cancel"
          @close="close"
          transition="slide-x-transition"
    >
      <div class="timestamp">{{ format(model) }}</div>
      <template v-slot:input>
        <div class="popup">
          <input
              :class="{ 'popup__text': true, 'popup__text--invalid': invalid }"
              :style="{ width: `${timestamp.length}ch` }"
              type="tel"
              autofocus="autofocus"
              placeholder="0:00"
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

const rule = /^\d:\d\d$/;

@Component
export default class Timestamp extends Vue {
  @Model('change', { type: Number }) private readonly model!: string;
  private timestamp = '';
  private invalid = false;

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

  @Watch('timestamp')
  onValueChanged() {
    this.invalid = false;
  }

  /**
   * Format the timestamp to Minutes and Seconds
   */
  format(timestamp) {
    return moment.utc(moment.duration(timestamp, 'seconds').asMilliseconds()).format('m:ss');
  }

  setTimeFromPlayer() {
    const timestamp = this.$store.state.player.seek;
    this.timestamp = this.format(timestamp);
  }

  reset() {
    this.timestamp = this.format(this.model);
    this.invalid = false;
  }

  cancel() {
    this.reset();
  }

  close() {
    // Validate format of timestamp.
    const value = this.timestamp.trim();

    if (!rule.test(value)) {
      // Hack-y way to add validation for now.
      // eslint-disable-next-line dot-notation
      this.$refs.dialog['isActive'] = true;
      this.invalid = true;
      return;
    }

    this.invalid = false;
    const parsed = moment.utc(this.timestamp, 'm:ss').diff(moment.utc().startOf('day'), 'seconds');
    this.$emit('change', parsed);
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
  min-width: 40px;
  margin-right: 4px;
  outline: none;
  font-family: 'Roboto Slab', serif;

  &--invalid {
    color: red;
    animation: shake 0.5s ease-out;
  }
}

@keyframes shake {
  10%, 90% {
    transform: translate3d(-1px, 0, 0);
  }

  30%, 50%, 70% {
    transform: translate3d(-3px, 0, 0);
  }

  40%, 60% {
    transform: translate3d(3px, 0, 0);
  }
}
</style>

<template>
  <div>
    <v-menu
      v-model="menu"
      :close-on-content-click="false"
      :attach="true"
      transition="slide-x-transition"
      nudge-left="12"
      nudge-top="12"
    >
      <template v-slot:activator="{ on }">
        <div class="timestamp" v-on="on">
          {{ format(model) }}
        </div>
      </template>
      <v-sheet class="popup">
        <input
          v-model="timestamp"
          :class="{ 'popup__text': true, 'popup__text--invalid': invalid }"
          :style="{ width: `${timestamp.length}ch` }"
          type="tel"
          autofocus="autofocus"
          placeholder="0:00"
          autocomplete="off"
          @keyup.enter="save"
        >
        <v-btn icon @click="setTimeFromPlayer">
          <v-icon>update</v-icon>
        </v-btn>
      </v-sheet>
    </v-menu>
  </div>
</template>

<script lang="ts">
import {
  Component, Model, Watch, Vue,
} from 'vue-property-decorator';
import moment from 'moment';

const rule = /^\d:\d\d$/;

@Component
export default class Timestamp extends Vue {
  @Model('change', { type: Number }) private readonly model!: string;
  private timestamp = '';
  private invalid = false;
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

  @Watch('timestamp')
  onValueChanged() {
    this.invalid = false;
  }

  @Watch('menu')
  onMenuToggled(menu: boolean) {
    if (menu) {
      this.reset();
    }
  }

  /**
   * Format the timestamp to Minutes and Seconds
   */
  format(timestamp) {
    return moment.utc(moment.duration(timestamp, 'seconds').asMilliseconds()).format('m:ss');
  }

  setTimeFromPlayer() {
    const timestamp = this.$store.state.player.seek;
    this.$emit('change', timestamp);
    this.menu = false;
  }

  reset() {
    this.timestamp = this.format(this.model);
    this.invalid = false;
  }

  cancel() {
    this.reset();
  }

  save() {
    // Validate format of timestamp.
    const value = this.timestamp.trim();

    if (!rule.test(value)) {
      // Hack-y way to add validation for now.
      // eslint-disable-next-line dot-notation
      this.menu = true;
      this.invalid = true;
      return;
    }

    this.invalid = false;
    const parsed = moment.utc(this.timestamp, 'm:ss').diff(moment.utc().startOf('day'), 'seconds');
    this.$emit('change', parsed);
    this.menu = false;
  }
}
</script>

<style lang="scss" scoped>
.timestamp {
  cursor: pointer;
  opacity: 0.6;
}
.popup {
  padding: 4px 12px;
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

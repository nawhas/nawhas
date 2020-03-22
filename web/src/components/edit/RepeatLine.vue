<template>
  <div class="repeat">
    <v-btn icon v-if="!repeat" @click="enableRepeat"><v-icon>loop</v-icon></v-btn>
    <v-menu
      v-else
      :close-on-content-click="false"
      v-model="menu"
      left bottom
      transition="scale-transition"
      origin="top right"
    >
      <template v-slot:activator="{ on }">
        <v-chip
          label
          color="primary"
          v-on="on"
        >
          <v-icon>loop</v-icon>
          <span class="repeat-value">{{ repeat }}</span>
        </v-chip>
      </template>
      <v-card>
        <v-card-title class="card-title">
          <v-icon class="mr-3">loop</v-icon>
          <div class="card-title__text">Repeat Line</div>
        </v-card-title>
        <v-card-text>
          <div class="repeat-actions">
            <v-btn icon @click="decrement"><v-icon>remove</v-icon></v-btn>
            <span class="repeat-value-text">{{ repeat }}</span>
            <v-btn icon @click="increment"><v-icon>add</v-icon></v-btn>
          </div>
        </v-card-text>
      </v-card>
    </v-menu>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class RepeatLine extends Vue {
  private repeat = 0;
  private menu = false;

  enableRepeat() {
    this.repeat = 2;
  }

  increment() {
    this.repeat++;
  }

  decrement() {
    if (this.repeat === 2) {
      this.repeat = 0;
      this.menu = false;
      return;
    }
    this.repeat--;
  }
}
</script>

<style lang="scss" scoped>
.repeat {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
}
.repeat-value {
  padding: 4px;
}
.repeat-value-text {
  font-size: 1.2rem;
  padding: 0 36px;
}
.repeat-actions {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}
.card-title__text {
  font-size: 1.2rem;
  font-weight: 500;
}
</style>

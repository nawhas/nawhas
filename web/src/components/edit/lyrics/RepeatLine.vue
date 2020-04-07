<template>
  <div class="repeat">
    <v-btn small icon v-if="!repeat" @click="enableRepeat" class="enable-repeat-icon">
      <v-icon>loop</v-icon>
    </v-btn>
    <v-menu
      v-else
      :close-on-content-click="false"
      v-model="menu"
      :attach="true"
      left bottom
      transition="scale-transition"
      origin="top right"
    >
      <template v-slot:activator="{ on }">
        <v-chip
            label
            outlined
            color="primary lighten-1"
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
import { Component, Model, Vue } from 'vue-property-decorator';

@Component
export default class RepeatLine extends Vue {
  @Model('change', { type: Number }) private readonly repeat!: number;
  private menu = false;

  enableRepeat() {
    this.$emit('change', 2);
  }

  increment() {
    this.$emit('change', this.repeat + 1);
  }

  decrement() {
    if (this.repeat === 2) {
      this.$emit('change', 0);
      this.menu = false;
      return;
    }
    this.$emit('change', this.repeat - 1);
  }
}
</script>

<style lang="scss" scoped>
.repeat {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
}
.enable-repeat-icon {
  opacity: 0.3;
  &:hover {
    opacity: 0.8;
  }
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

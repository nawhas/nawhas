<template>
  <v-row :dense="$vuetify.breakpoint.smAndDown">
    <v-col v-for="index in limit" :key="index" cols="12" sm="6" md="4">
      <slot>
        <v-skeleton-loader type="list-item-avatar-two-line" elevation="2" />
      </slot>
    </v-col>
  </v-row>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';

@Component
export default class SkeletonCardGrid extends Vue {
  @Prop({ type: Number, default: 6 }) private limit!: number;
}
</script>

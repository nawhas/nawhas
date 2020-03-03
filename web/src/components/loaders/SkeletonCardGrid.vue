<template>
  <v-row :dense="$vuetify.breakpoint.smAndDown">
    <v-col cols="12" sm="6" md="4" v-for="index in limit" :key="index">
      <v-skeleton-loader type="list-item-avatar-two-line" elevation="2"></v-skeleton-loader>
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

<template>
  <v-layout row wrap>
    <v-flex xs12 v-for="index in 6" :key="index">
      <v-skeleton-loader
        ref="skeleton"
        type="list-item-avatar-three-line"
        class="mx-auto"
      ></v-skeleton-loader>
    </v-flex>
  </v-layout>
</template>

<script>
export default {
  name: 'MoreTracksSkeleton',
};
</script>

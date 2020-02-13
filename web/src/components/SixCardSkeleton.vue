<template>
  <v-layout row wrap>
    <v-flex xs12 sm6 md4 v-for="index in 6" :key="index">
      <v-skeleton-loader
        ref="skeleton"
        :boilerplate="false"
        type="list-item-avatar-three-line"
        :tile="false"
        class="mx-auto"
      ></v-skeleton-loader>
    </v-flex>
  </v-layout>
</template>

<script>
export default {
  name: 'skeleton.six.cards',
};
</script>

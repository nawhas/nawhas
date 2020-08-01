<template>
  <v-layout row wrap>
    <v-flex v-for="index in 6" :key="index" xs12>
      <v-skeleton-loader
        ref="skeleton"
        type="list-item"
        class="mx-auto mt-1"
      />
    </v-flex>
  </v-layout>
</template>

<template>
  <v-layout row wrap>
    <v-flex xs12 v-for="index in 6" :key="index">
      <v-skeleton-loader
        ref="skeleton"
        type="list-item"
        class="mx-auto mt-1"
      ></v-skeleton-loader>
    </v-flex>
  </v-layout>
</template>

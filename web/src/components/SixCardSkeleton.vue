<template>
  <v-row>
    <v-col cols="12" sm="6" md="4" v-for="index in 6" :key="index">
      <v-skeleton-loader type="list-item-avatar-two-line" elevation="2"></v-skeleton-loader>
    </v-col>
  </v-row>
</template>

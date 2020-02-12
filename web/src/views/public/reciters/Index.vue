<template>
  <div>
    <section class="page-section" id="top-reciters-section">
      <h5 class="title">Top Reciters</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <v-layout row wrap>
          <v-flex xs12 sm6 md4 v-for="reciter in popularReciters" :key="reciter.id">
            <reciter-card featured v-bind="reciter" />
          </v-flex>
        </v-layout>
      </v-container>
    </section>

    <section class="page-section" id="all-reciters-section">
      <h5 class="title">All Reciters</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <v-layout row wrap>
          <v-flex xs12 sm6 md4 v-for="reciter in reciters" :key="reciter.id">
            <reciter-card v-bind="reciter" />
          </v-flex>
        </v-layout>
      </v-container>
    </section>
  </div>
</template>

<script lang="ts">
import ReciterCard from '@/components/ReciterCard.vue';
import { mapGetters } from 'vuex';
import store from '@/store';
import { Route } from 'vue-router';

async function fetchData() {
  await Promise.all([
    store.dispatch('popular/fetchPopularReciters', { limit: 6 }),
    store.dispatch('reciters/fetchReciters', { limit: 6 }),
  ]);
}

export default {
  name: 'Reciters',
  components: { ReciterCard },
  async beforeRouteEnter(to: Route, from: Route, next: (to?: any) => void) {
    await fetchData();
    next();
  },
  async beforeRouteUpdate(to: Route, from: Route, next: (to?: any) => void) {
    await fetchData();
    next();
  },
  computed: {
    ...mapGetters({
      popularReciters: 'popular/popularReciters',
      reciters: 'reciters/reciters',
    }),
  },
};
</script>

<style lang="scss" scoped>
.title {
  margin-bottom: 12px;
}
</style>

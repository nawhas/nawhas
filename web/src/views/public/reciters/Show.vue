<template>
  <div>
    <section class="page-section" id="top-reciters-section">
      <h5 class="title">Top Nawhas</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <v-layout row wrap>
          <v-flex xs12 sm6 md4 v-for="track in popularTracks" v-bind:key="track.id">
            <track-card v-bind="track" :show-reciter="false"></track-card>
          </v-flex>
        </v-layout>
      </v-container>
    </section>
  </div>
</template>

<script lang="ts">
import { mapGetters } from 'vuex';
import TrackCard from '@/components/TrackCard.vue';
import store from '@/store';

async function fetchData(reciter) {
  await Promise.all([
    store.dispatch('albums/fetchAlbums', { reciter }),
    store.dispatch('reciters/fetchReciter', { reciter }),
  ]);
  await store.dispatch('popular/fetchPopularTracks', { limit: 6, reciterId: store.getters['reciters/reciter'].id });
}

export default {
  name: 'reciters.show',
  components: {
    TrackCard,
  },
  async beforeRouteEnter(to, from, next) {
    await fetchData(to.params.reciter);
    next();
  },
  async beforeRouteUpdate(to, from, next) {
    await fetchData(to.params.reciter);
    next();
  },
  computed: {
    ...mapGetters({
      reciter: 'reciters/reciter',
      albums: 'albums/albums',
      popularTracks: 'popular/popularTracks',
    }),
  },
};
</script>

<style lang="scss" scoped>
.title {
  margin-bottom: 12px;
}
</style>

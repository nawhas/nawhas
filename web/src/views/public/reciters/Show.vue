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

    <section class="page-section" id="all-reciters-section">
      <h5 class="title">Albums</h5>
      <template v-for="album in albums">
        <album v-bind="album" :reciter="reciter" v-bind:key="album.id"></album>
      </template>
    </section>
  </div>
</template>

<script lang="ts">
import { mapGetters } from 'vuex';
import store from '@/store';
import TrackCard from '@/components/TrackCard.vue';
import Album from '@//components/Album.vue';

async function fetchData(reciter) {
  await Promise.all([
    store.dispatch('albums/fetchAlbums', { reciter }),
    store.dispatch('reciters/fetchReciter', { reciter }),
  ]);
  await store.dispatch('popular/fetchPopularTracks', {
    limit: 6,
    reciterId: store.getters['reciters/reciter'].id,
  });
}

export default {
  name: 'reciters.show',
  components: {
    TrackCard,
    Album,
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

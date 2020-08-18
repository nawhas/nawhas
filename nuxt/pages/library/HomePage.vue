<router>
  path: /library/home
  name: "LibraryHomePage"
</router>

<template>
  <div>
    <page-header>
      <v-icon size="64">
        local_library
      </v-icon>Library
    </page-header>

    <v-container class="app__section">
      <h5 class="section__title mt-6">
        <v-icon>favorite</v-icon> Recently Saved Nawhas
      </h5>
      <template v-if="popularTracks">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="track in popularTracks" :key="track.id" cols="12" sm="6" md="4">
            <track-card :track="track" :colored="true" :show-reciter="true" />
          </v-col>
        </v-row>
      </template>
      <template v-else>
        <skeleton-card-grid>
          <track-card-skeleton />
        </skeleton-card-grid>
      </template>
    </v-container>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import TrackCard from '@/components/tracks/TrackCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import TrackCardSkeleton from '@/components/loaders/TrackCardSkeleton.vue';
import { Track } from '@/entities/track';
import { TrackIncludes } from '@/api/tracks';

const POPULAR_ENTITIES_LIMIT = 6;

interface Data {
  tracks: Array<Track>|null;
}

export default Vue.extend({
  components: {
    PageHeader,
    TrackCard,
    SkeletonCardGrid,
    TrackCardSkeleton,
  },
  async fetch() {
    const response = await this.$api.tracks.popular({
      include: [
        TrackIncludes.Reciter,
        TrackIncludes.Lyrics,
        TrackIncludes.Media,
        TrackIncludes.Related,
        'album.tracks',
      ],
    });
    this.tracks = response.data;
  },
  data: (): Data => ({
    tracks: null,
  }),
  computed: {
    popularTracks() {
      return this.tracks ? this.tracks.slice(0, POPULAR_ENTITIES_LIMIT) : null;
    },
  },
});
</script>

<style scoped>

</style>

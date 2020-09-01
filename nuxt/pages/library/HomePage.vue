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
      <h5 class="section__title mt-6 d-flex align-center justify-start">
        <v-icon class="mr-2">favorite</v-icon> Recently Saved Nawhas
      </h5>
      <template v-if="tracks">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="track in tracks" :key="track.id" cols="12" sm="6" md="4">
            <track-card :track="track" :colored="true" :show-reciter="true" />
          </v-col>
        </v-row>
        <v-row>
          <v-col class="text-center" @click="$router.push('/library/tracks')">
            <v-btn color="primary">
              View All
            </v-btn>
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

interface Data {
  tracks: Array<Track>|null;
}

export default Vue.extend({
  middleware({ store, redirect }) {
    if (!store.state.auth.user) {
      return redirect('/library');
    }
  },
  components: {
    PageHeader,
    TrackCard,
    SkeletonCardGrid,
    TrackCardSkeleton,
  },
  async fetch() {
    const response = await this.$api.library.tracks({
      include: [
        TrackIncludes.Reciter,
        TrackIncludes.Lyrics,
        TrackIncludes.Media,
        TrackIncludes.Related,
        'album.tracks',
      ],
      pagination: {
        limit: 6,
      },
    });
    this.tracks = response.data;
  },
  data: (): Data => ({
    tracks: null,
  }),
  watch: {
    '$store.state.auth.user': 'onAuthChange',
  },
  methods: {
    onAuthChange(value) {
      if (!value) {
        this.$router.replace('/library');
      }
    },
  },
});
</script>

<style scoped>

</style>

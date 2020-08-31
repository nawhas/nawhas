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
      <h5 class="section__title section__title--with-actions mt-6">
        <div>
          <v-icon>favorite</v-icon> Recently Saved Nawhas
        </div>
        <v-btn text @click="playSavedTracks">
          Play All
        </v-btn>
      </h5>
      <template v-if="tracks">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="track in tracks" :key="track.id" cols="12" sm="6" md="4">
            <track-card :track="track" :colored="true" :show-reciter="true" />
          </v-col>
        </v-row>
        <v-row>
          <v-col class="text-center">
            <v-btn color="primary">View All</v-btn>
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
    await this.$store.dispatch('library/getTracks');
  },
  computed: {
    playable(): Array<Track> {
      if (!this.tracks) {
        return [];
      }

      return this.tracks.filter((track) => this.hasAudioFile(track));
    },
    tracks(): Array<Track> {
      return this.$store.state.library.tracks;
    },
  },
  methods: {
    playSavedTracks() {
      this.$store.commit('player/PLAY_ALBUM', { tracks: this.playable, start: this.playable[0] });
    },
    hasAudioFile(track): boolean {
      return track.related?.audio ?? false;
    },
  },
});
</script>

<style scoped>

</style>

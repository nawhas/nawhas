<router>
  path: /library/tracks
  name: "library.tracks"
</router>

<template>
  <div>
    <library-header />
    <v-container class="app__section">
      <h5 class="section__title section__title--with-actions mt-6">
        <div class="d-flex align-center justify-start">
          <v-icon class="mr-2">
            favorite
          </v-icon>Saved Nawhas
        </div>
        <v-btn text @click="playSavedTracks">
          Play All
        </v-btn>
      </h5>
      <v-card>
        <track-list :tracks="tracks" metadata :display-avatar="true" />
      </v-card>
      <v-pagination
        v-model="page"
        color="deep-orange"
        class="mt-6"
        :length="length"
        circle
        next-icon="navigate_next"
        prev-icon="navigate_before"
        @input="onPageChanged"
      />
    </v-container>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { TrackIncludes } from '@/api/tracks';
import { Track } from '@/entities/track';
import TrackList from '@/components/tracks/TrackList.vue';
import { getPage } from '@/utils/route';
import LibraryHeader from '@/components/library/LibraryHeader.vue';

interface Data {
  tracks: Array<Track>|null;
  page: number;
  length: number;
}

export default Vue.extend({
  middleware({ store, redirect }) {
    if (!store.state.auth.user) {
      return redirect('/library');
    }
  },
  components: {
    TrackList,
    LibraryHeader,
  },
  async fetch() {
    await this.getTracks();
  },
  data(): Data {
    const page = getPage(this.$route);

    return {
      tracks: null,
      page,
      length: 1,
    };
  },
  computed: {
    playable(): Array<Track> {
      if (!this.tracks) {
        return [];
      }

      return this.tracks.filter((track) => this.hasAudioFile(track));
    },
  },
  watch: {
    '$store.state.auth.user': 'onAuthChange',
    '$route.query': 'getTracks',
  },
  methods: {
    onAuthChange(value) {
      if (!value) {
        this.$router.replace('/library');
      }
    },
    playSavedTracks() {
      this.$store.commit('player/PLAY_ALBUM', { tracks: this.playable, start: this.playable[0] });
    },
    hasAudioFile(track): boolean {
      return track.related?.audio ?? false;
    },
    async getTracks() {
      const response = await this.$api.library.tracks({
        include: [
          TrackIncludes.Reciter,
          TrackIncludes.Lyrics,
          TrackIncludes.Media,
          TrackIncludes.Related,
          'album.tracks',
        ],
        pagination: {
          limit: 20,
          page: getPage(this.$route),
        },
      });
      this.tracks = response.data;
      this.length = response.meta.pagination.total_pages;
    },
    onPageChanged(page) {
      this.$router.push({ query: { page: String(page) } });
    },
  },
});
</script>

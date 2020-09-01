<router>
  path: /library/tracks
  name: "LibraryTracksPage"
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
          <v-icon>favorite</v-icon>Saved Nawhas
        </div>
      </h5>
    </v-container>

    <v-container class="app__section">
      <v-card>
        <track-list :tracks="tracks" metadata :display-avatar="true" />
      </v-card>
    </v-container>

    <v-container class="app__section">
      <v-pagination
        v-model="page"
        color="deep-orange"
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
          limit: 10,
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

<style scoped>

</style>

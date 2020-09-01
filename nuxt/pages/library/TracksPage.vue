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
        <v-menu offset-y>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              color="primary"
              v-bind="attrs"
              v-on="on"
            >
              Sort
            </v-btn>
          </template>
          <v-list>
            <v-list-item
              v-for="(item, index) in sortDropdown"
              :key="index"
              @click="onSort(index)"
            >
              <v-list-item-title>{{ item.title }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </h5>
    </v-container>

    <v-container class="app__section">
      <v-card>
        <track-list :tracks="tracks" metadata :display-avatar="true" />
      </v-card>
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
  sortDropdown: Array<object>;
  page: number;
  length: number;
  sort: 'asc' | 'desc';
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
    await this.getTracks('asc');
  },
  data(): Data {
    const page = getPage(this.$route);

    return {
      tracks: null,
      sortDropdown: [
        { title: 'Ascending' },
        { title: 'Descending' },
      ],
      page,
      length: 1,
      sort: 'asc',
    };
  },
  watch: {
    '$store.state.auth.user': 'onAuthChange',
    '$route.query': 'getTracks',
    'sort': 'onSortChange',
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
        sortDir: this.sort,
      });
      this.tracks = response.data;
      this.length = response.meta.pagination.total_pages;
    },
    onSort(value) {
      if (value === 0) {
        this.sort = 'asc';
      } else {
        this.sort = 'desc';
      }
    },
    onPageChanged(page) {
      this.$router.push({ query: { page: String(page) } });
    },
    onSortChange() {
      this.onPageChanged(1);
      this.getTracks();
    },
  },
});
</script>

<style scoped>

</style>

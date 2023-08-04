<router>
  path: /
  name: "home"
</router>

<template>
  <div>
    <header :class="{ 'header': true, 'header--dark': $vuetify.theme.dark }">
      <v-container class="app__section">
        <h1 class="header__title">
          Explore the most advanced library of nawhas online.
        </h1>
      </v-container>
      <div class="search">
        <global-search hero />
      </div>
    </header>

    <v-container class="app__section">
      <h5 class="section__title mt-6">
        Trending This Month
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

    <v-container class="app__section">
      <div class="section__title mt-6">
        <div>
          <v-icon>favorite</v-icon> Recently Saved Nawhas
        </div>
      </div>
      <template v-if="savedTracks && savedTracks.length > 0">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="track in savedTracks" :key="track.id" cols="12" sm="6" md="4">
            <track-card :track="track" :colored="true" :show-reciter="true" />
          </v-col>
        </v-row>
        <v-row>
          <v-col class="text-center">
            <v-btn color="primary" to="/library/tracks">
              View All
            </v-btn>
          </v-col>
        </v-row>
      </template>
      <template v-else-if="savedTracksLoading">
        <skeleton-card-grid>
          <track-card-skeleton />
        </skeleton-card-grid>
      </template>
      <template v-else>
        <saved-tracks-empty-state />
      </template>
    </v-container>

    <hero-banner :background="require('@/assets/img/backgrounds/imam-hussain-header.jpg')" class="my-12">
      <hero-quote author="Imam Jafar Sadiq (a.s.)">
        The murder of Hussain has lit a fire in the hearts of the believers which will never
        extinguish.
      </hero-quote>
    </hero-banner>

    <v-container class="app__section">
      <div class="section__title section__title--with-actions">
        <div>Top Reciters</div>
        <v-btn text to="/reciters">
          View All
        </v-btn>
      </div>
      <template v-if="popularReciters">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="reciter in popularReciters" :key="reciter.id" md="4" sm="6" cols="12">
            <reciter-card featured v-bind="reciter" />
          </v-col>
        </v-row>
      </template>
      <template v-else>
        <skeleton-card-grid />
      </template>
    </v-container>

    <v-container class="app__section">
      <h5 class="section__title mb-4">
        Top Nawhas
      </h5>
      <v-card>
        <track-list :tracks="tracks" metadata numbered :count="20" />
      </v-card>
    </v-container>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import HeroBanner from '@/components/HeroBanner.vue';
import HeroQuote from '@/components/HeroQuote.vue';
import ReciterCard from '@/components/ReciterCard.vue';
import TrackCard from '@/components/tracks/TrackCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import TrackCardSkeleton from '@/components/loaders/TrackCardSkeleton.vue';
import TrackList from '@/components/tracks/TrackList.vue';
import GlobalSearch from '@/components/search/GlobalSearch.vue';
import { Reciter } from '@/entities/reciter';
import { Track } from '@/entities/track';
import { ReciterIncludes } from '@/api/reciters';
import { TrackIncludes } from '@/api/tracks';
import SavedTracksEmptyState from '@/components/library/SavedTracksEmptyState.vue';

const POPULAR_ENTITIES_LIMIT = 6;

interface Data {
  reciters: Array<Reciter> | null;
  tracks: Array<Track> | null;
  savedTracks: Array<Track> | null;
  savedTracksLoading: boolean;
}

export default Vue.extend({
  components: {
    SavedTracksEmptyState,
    TrackList,
    HeroBanner,
    HeroQuote,
    ReciterCard,
    TrackCard,
    SkeletonCardGrid,
    TrackCardSkeleton,
    GlobalSearch,
  },

  data: (): Data => ({
    reciters: null,
    tracks: null,
    savedTracks: null,
    savedTracksLoading: false,
  }),

  async fetch() {
    const [reciters, tracks] = await Promise.all([
      this.$api.reciters.popular({
        pagination: { limit: 6 },
        include: [ReciterIncludes.Related],
      }),
      this.$api.tracks.popular({
        pagination: { limit: 20 },
        include: [TrackIncludes.Reciter, TrackIncludes.Album, TrackIncludes.Media, TrackIncludes.Related],
      }),
      this.getSavedTracks(),
    ]);
    this.reciters = reciters.data;
    this.tracks = tracks.data;
  },

  computed: {
    popularReciters(): Array<Reciter> | null {
      return this.reciters ? this.reciters.slice(0, POPULAR_ENTITIES_LIMIT) : null;
    },

    popularTracks(): Array<Track> | null {
      return this.tracks ? this.tracks.slice(0, POPULAR_ENTITIES_LIMIT) : null;
    },
  },

  watch: {
    '$store.state.auth.user': 'onAuthChange',
    '$store.state.library.trackIds': 'getSavedTracks',
  },

  methods: {
    async getSavedTracks() {
      if (!this.$store.getters['auth/user']) {
        return;
      }
      this.savedTracksLoading = true;
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
      this.savedTracks = response.data;
      this.savedTracksLoading = false;
    },
    onAuthChange(value) {
      if (value) {
        this.getSavedTracks();
      } else {
        this.savedTracks = null;
      }
    },
  },
});
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.header {
  width: 100%;
  background: radial-gradient(100% 100% at 50% 0%, #B10016 0%, #93291E 100%);
  margin-bottom: 48px;
  position: relative;
  color: white;
}
.header--dark {
  background: radial-gradient(100% 100% at 50% 0%, #93291E 0%, #121212 100%);
}
.search {
  position: absolute;
  height: 38px;
  bottom: 0;
  left: 0;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  z-index: 2;
}
.header .app__section {
  padding: 96px !important;
}
.header__title {
  font-family: Roboto, sans-serif;
  font-weight: 200;
  font-size: 64px;
  line-height: 75px;
  text-align: center;
  letter-spacing: -1.5px;
}

@include breakpoint('md-and-down') {
  .header .app__section {
    padding: 64px !important;
  }
  .header__title {
    font-size: 48px;
  }
}
@include breakpoint('sm-and-down') {
  .search {
    height: 30px;
  }
}
@include breakpoint('xs-only') {
  .header .app__section {
    padding: 48px 48px 64px !important;
  }
  .header__title {
    font-size: 32px;
    line-height: initial;
  }
}
</style>

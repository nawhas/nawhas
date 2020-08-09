<template>
  <div>
    <header :class="{ 'header': true, 'header--dark': $vuetify.theme.dark }">
      <v-container class="app__section">
        <h1 class="header__title">
          Explore the most advanced library of nawhas online.
        </h1>
      </v-container>
      <div class="search">
        <!-- <global-search hero /> -->
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

    <hero-banner background="/backgrounds/imam-hussain-header.jpg" class="my-12">
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
        <track-list :tracks="tracks" metadata :count="20" />
      </v-card>
    </v-container>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'nuxt-property-decorator';
import HeroBanner from '@/components/HeroBanner.vue';
import HeroQuote from '@/components/HeroQuote.vue';
import ReciterCard from '@/components/ReciterCard.vue';
import TrackCard from '@/components/tracks/TrackCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import TrackCardSkeleton from '@/components/loaders/TrackCardSkeleton.vue';
import TrackList from '@/components/tracks/TrackList.vue';

const POPULAR_ENTITIES_LIMIT = 6;

@Component({
  components: {
    TrackList,
    HeroBanner,
    HeroQuote,
    ReciterCard,
    TrackCard,
    SkeletonCardGrid,
    TrackCardSkeleton,
  },
})
export default class HomeView extends Vue {
  private reciters: any = null;
  private tracks: any = null;

  get popularReciters() {
    return this.reciters ? this.reciters.slice(0, POPULAR_ENTITIES_LIMIT) : null;
  }

  get popularTracks() {
    return this.tracks ? this.tracks.slice(0, POPULAR_ENTITIES_LIMIT) : null;
  }

  async fetch() {
    const [reciters, tracks] = await Promise.all([
      this.$axios.$get('v1/popular/reciters?per_page=20&include=related'),
      this.$axios.$get('v1/popular/tracks?per_page=20&include=reciter,lyrics,album.tracks,media,related'),
    ]);

    this.reciters = reciters.data;
    this.tracks = tracks.data;
  }
}
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.header {
  width: 100%;
  background-color: rgba(255, 21, 0, 0.1);
  margin-bottom: 48px;
  position: relative;
}
.header--dark {
  background: linear-gradient(30deg, #340808, #1a0000);
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

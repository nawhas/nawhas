<template>
  <div>
    <hero-banner :background="require('../assets/imam-hussain-header.jpg')" class="mb-4">
      <hero-quote author="Imam Jafar Sadiq (a.s.)">
        The murder of Hussain has lit a fire in the hearts of the believers which will never
        extinguish.
      </hero-quote>
    </hero-banner>
    <section class="page-section" id="top-reciters-section">
      <h5 class="title">Top Reciters</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <template v-if="!loading">
          <v-layout row wrap>
            <v-flex xs12 sm6 md4 v-for="reciter in popularReciters" :key="reciter.id">
              <reciter-card featured v-bind="reciter" />
            </v-flex>
          </v-layout>
        </template>
        <template v-else>
          <six-card-skeleton />
        </template>
      </v-container>
    </section>
    <section class="page-section" id="trending-nawhas">
      <h5 class="title">Trending Nawhas</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <template v-if="!loading">
          <v-layout row wrap>
            <v-flex xs12 sm6 md4 v-for="track in popularTracks" v-bind:key="track.id">
              <track-card v-bind="track" :show-reciter="true" />
            </v-flex>
          </v-layout>
        </template>
        <template v-else>
          <six-card-skeleton />
        </template>
      </v-container>
    </section>
  </div>
</template>

<script>
import HeroBanner from '@/components/HeroBanner.vue';
import HeroQuote from '@/components/HeroQuote.vue';
import ReciterCard from '@/components/ReciterCard.vue';
import TrackCard from '@/components/TrackCard.vue';
import SixCardSkeleton from '@/components/SixCardSkeleton.vue';
import { getPopularReciters, getPopularTracks } from '@/services/popular';

export default {
  name: 'Home',
  components: {
    HeroBanner,
    HeroQuote,
    ReciterCard,
    TrackCard,
    SixCardSkeleton,
  },
  async mounted() {
    this.loading = true;
    const [popularReciters, popularTracks] = await Promise.all([
      getPopularReciters({ limit: 6 }),
      getPopularTracks({ limit: 6, include: 'reciter,album' }),
    ]);

    this.setData(popularReciters, popularTracks);
    this.loading = false;
  },
  data() {
    return {
      popularReciters: [],
      popularTracks: [],
      loading: false,
    };
  },
  methods: {
    setData(popularReciters, popularTracks) {
      this.popularReciters = popularReciters.data.data;
      this.popularTracks = popularTracks.data.data;
    },
  },
};
</script>

<style lang="scss" scoped>
.title {
  margin-bottom: 12px;
}
</style>

<template>
  <div>
    <hero-banner :background="require('../../assets/imam-hussain-header.jpg')" class="mb-4">
      <hero-quote author="Imam Jafar Sadiq (a.s.)">
        The murder of Hussain has lit a fire in the hearts of the believers which will never
        extinguish.
      </hero-quote>
    </hero-banner>
    <v-container class="app__section">
      <h5 class="headline">Top Reciters</h5>
      <template v-if="popularReciters">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="reciter in popularReciters" :key="reciter.id" md="4" sm="6" cols="12">
            <reciter-card featured v-bind="reciter" />
          </v-col>
        </v-row>
      </template>
      <template v-else>
        <six-card-skeleton />
      </template>
    </v-container>
    <v-container class="app__section">
      <h5 class="headline">Trending Nawhas</h5>
      <template v-if="popularTracks">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col cols="12" sm="6" md="4" v-for="track in popularTracks" v-bind:key="track.id">
            <track-card v-bind="track" :show-reciter="true" />
          </v-col>
        </v-row>
      </template>
      <template v-else>
        <six-card-skeleton />
      </template>
    </v-container>
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
  mounted() {
    getPopularReciters({ per_page: 6 }).then((response) => {
      this.popularReciters = response.data.data;
    });
    getPopularTracks({ per_page: 6, include: 'reciter,album' }).then((response) => {
      this.popularTracks = response.data.data;
    });
  },
  data() {
    return {
      popularReciters: null,
      popularTracks: null,
    };
  },
};
</script>

<style lang="scss" scoped>
.headline {
  margin-bottom: 12px;
}
</style>

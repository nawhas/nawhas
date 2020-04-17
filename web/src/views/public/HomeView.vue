<template>
  <div>
    <hero-banner :background="require('../../assets/imam-hussain-header.jpg')" class="mb-4">
      <hero-quote author="Imam Jafar Sadiq (a.s.)">
        The murder of Hussain has lit a fire in the hearts of the believers which will never
        extinguish.
      </hero-quote>
    </hero-banner>

    <v-container class="app__section">
      <h5 class="section__title">Trending This Month</h5>
      <template v-if="popularTracks">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col cols="12" sm="6" md="4" v-for="track in popularTracks" v-bind:key="track.id">
            <track-card v-bind="track" :colored="true" :show-reciter="true" />
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
      <div class="section__title section__title--with-actions">
        <div>Top Reciters</div>
        <v-btn text :to="{ name: 'reciters.index' }">View All</v-btn>
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
      <h5 class="section__title mb-4">Top Nawhas</h5>
      <v-card>
        <track-list :tracks="tracks" metadata :count="20" />
      </v-card>
    </v-container>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import HeroBanner from '@/components/HeroBanner.vue';
import HeroQuote from '@/components/HeroQuote.vue';
import ReciterCard from '@/components/ReciterCard.vue';
import TrackCard from '@/components/TrackCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import TrackCardSkeleton from '@/components/loaders/TrackCardSkeleton.vue';
import { getPopularReciters, getPopularTracks } from '@/services/popular';
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

  mounted() {
    getPopularReciters({ per_page: 20, include: 'related' }).then((response) => {
      this.reciters = response.data.data;
    });
    getPopularTracks({ per_page: 20, include: 'reciter,lyrics,album.tracks,media,related' }).then((response) => {
      this.tracks = response.data.data;
    });
  }
}
</script>

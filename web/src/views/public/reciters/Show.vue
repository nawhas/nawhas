<template>
  <div>
    <div class="reciter-hero" v-if="reciter">
      <div class="reciter-hero__ribbon"></div>
      <div class="reciter-hero__content">
        <v-card class="reciter-hero__card">
          <div class="reciter-hero__avatar">
            <v-avatar size="152px" class="white">
              <img :src="image" :alt="reciter.name" />
            </v-avatar>
          </div>
          <h4 class="reciter-hero__title">{{ reciter.name }}</h4>
          <p class="reciter-hero__bio">{{ reciter.description }}</p>
        </v-card>
      </div>
    </div>
    <div v-else>
      <reciter-hero-skeleton />
    </div>
    <section class="page-section" id="top-reciters-section">
      <h5 class="title">Top Nawhas</h5>
      <v-container grid-list-lg class="pa-0" fluid>
        <template v-if="popularTracks">
          <v-layout row wrap>
            <v-flex xs12 sm6 md4 v-for="track in popularTracks" v-bind:key="track.id">
              <track-card v-bind="track" :show-reciter="false"></track-card>
            </v-flex>
          </v-layout>
        </template>
        <template v-else>
          <six-card-skeleton />
        </template>
      </v-container>
    </section>

    <section class="page-section" id="all-reciters-section">
      <h5 class="title">Albums</h5>
      <template v-if="albums">
        <template v-for="album in albums">
          <album v-bind="album" :reciter="reciter" v-bind:key="album.id"></album>
        </template>
      </template>
      <template v-else>
        <album-table-skeleton />
      </template>
    </section>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import TrackCard from '@/components/TrackCard.vue';
import ReciterHeroSkeleton from '@/components/ReciterHeroSkeleton.vue';
import SixCardSkeleton from '@/components/SixCardSkeleton.vue';
import AlbumTableSkeleton from '@/components/AlbumTableSkeleton.vue';
import Album from '@//components/Album.vue';

export default {
  name: 'reciters.show',
  components: {
    TrackCard,
    Album,
    ReciterHeroSkeleton,
    SixCardSkeleton,
    AlbumTableSkeleton,
  },
  async mounted() {
    const { reciter } = this.$route.params;
    await this.$store.dispatch('reciters/fetchReciter', { reciter });
    this.$store.dispatch('albums/fetchAlbums', { reciter: this.reciter.slug });
    this.$store.dispatch('popular/fetchPopularTracks', {
      limit: 6,
      reciterId: this.reciter.id,
    });
  },
  computed: {
    ...mapGetters({
      reciter: 'reciters/reciter',
      albums: 'albums/albums',
      popularTracks: 'popular/popularTracks',
    }),
    image() {
      return this.avatar || '/img/default-reciter-avatar.png';
    },
  },
};
</script>

<style lang="scss" scoped>
.reciter-hero {
  .reciter-hero__ribbon {
    width: 100%;
    height: 220px;
    margin-bottom: -220px;
    background: linear-gradient(to bottom right, #e90500, #fa6000);
  }

  .reciter-hero__content {
    padding: 80px 120px 24px 120px;
  }

  .reciter-hero__avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    top: -80px;
    margin-bottom: -56px;

    .avatar {
      box-sizing: content-box;
      border: 5px solid white;
    }
  }

  .reciter-hero__card {
    margin-top: 36px;
    width: 100%;
    min-height: 20px;
    position: relative;
    padding: 0 36px 24px 36px;
  }

  .reciter-hero__title {
    font-family: 'Roboto Slab', sans-serif;
    font-weight: 600;
    color: #2e2e2e;
    text-align: center;
    margin: 0;
    padding: 0;
  }

  .reciter-hero__bio {
    margin: 16px 0 0 0;
    padding: 0;
    max-height: 108px;
    overflow: hidden;
    position: relative;
  }
}
.title {
  margin-bottom: 12px;
}
</style>

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
          <skeleton-card-grid />
        </template>
      </v-container>
    </section>

    <section class="page-section" id="all-reciters-section">
      <h5 class="title">Albums</h5>
      <template v-if="albums">
        <template v-if="albums.length > 0">
          <template v-for="album in albums">
            <album v-bind="album" :reciter="reciter" v-bind:key="album.id"></album>
          </template>
          <v-pagination v-model="page" :length="length" circle @input="goToPage"></v-pagination>
        </template>
        <template v-else>
          <p>Sorry there are no albums currently.</p>
        </template>
      </template>
      <template v-else>
        <album-table-skeleton />
      </template>
    </section>
  </div>
</template>

<script>
import TrackCard from '@/components/TrackCard.vue';
import ReciterHeroSkeleton from '@/components/ReciterHeroSkeleton.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import AlbumTableSkeleton from '@/components/AlbumTableSkeleton.vue';
import { getReciter } from '@/services/reciters';
import { getAlbums } from '@/services/albums';
import { getPopularTracks } from '@/services/popular';
import Album from '@//components/Album.vue';

export default {
  name: 'ReciterProfile',
  components: {
    TrackCard,
    Album,
    ReciterHeroSkeleton,
    SkeletonCardGrid,
    AlbumTableSkeleton,
  },
  watch: {
    // call again the method if the route changes
    $route: 'fetchData',
  },
  created() {
    this.fetchData();
  },
  computed: {
    image() {
      return this.avatar || '/img/default-reciter-avatar.png';
    },
  },
  data() {
    return {
      page: 1,
      reciter: null,
      albums: null,
      length: 0,
      popularTracks: null,
    };
  },
  methods: {
    async fetchData() {
      this.reciter = null;
      this.albums = null;
      this.length = 0;
      this.popularTracks = null;

      const { reciter } = this.$route.params;
      getReciter(reciter).then((response) => {
        this.reciter = response.data;
      });
      getPopularTracks({
        per_page: 6,
        reciterId: reciter,
        include: 'album,reciter',
      }).then((response) => {
        this.popularTracks = response.data.data;
      });
      getAlbums(reciter, { include: 'tracks' }).then((response) => {
        this.setAlbums(response);
      });
    },
    setAlbums(albums) {
      this.albums = albums.data.data;
      this.length = albums.data.meta.pagination.total_pages;
    },
    goToPage(number) {
      this.albums = null;
      getAlbums(this.reciter.id, { include: 'tracks', page: number }).then((response) => {
        this.setAlbums(response);
      });
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

.v-pagination {
  margin-top: 16px;
}
</style>

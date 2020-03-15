<template>
  <div class="reciter-profile">
    <hero-banner :class="{
        'reciter-profile__hero': true,
        'reciter-profile__hero--with-toolbar': showToolbar
    }" :background="require('../../../assets/azadari-flags.jpg')">
      <div class="hero__content">
        <template v-if="reciter">
          <div class="hero__avatar">
            <v-avatar class="hero__avatar__component" :size="heroAvatarSize">
              <img :src="image" :alt="reciter ? reciter.name : 'Loading'" />
            </v-avatar>
          </div>
          <div class="hero__details">
            <div class="hero__title" v-if="reciter">{{ reciter.name }}</div>
            <div class="hero__title" v-else>

            </div>
          </div>
        </template>
        <v-skeleton-loader type="text" dark width="150px" v-else></v-skeleton-loader>
      </div>
      <div class="hero__bar" v-if="showToolbar">
        <v-container class="bar__content">
          <div class="bar__actions bar__actions--visible">
            <v-btn dark text><v-icon left>public</v-icon> Website</v-btn>
            <v-btn dark text><v-icon left>star_outline</v-icon> Favorite</v-btn>
          </div>
          <div class="bar__actions bar__actions--overflow">
            <v-btn dark icon><v-icon>more_vert</v-icon></v-btn>
          </div>
        </v-container>
      </div>
    </hero-banner>
    <v-container class="app__section">
      <h5 class="section__title">Top Nawhas</h5>
      <template v-if="popularTracks">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col cols="12" sm="6" md="4" v-for="track in popularTracks" v-bind:key="track.id">
            <track-card v-bind="track" :colored="true" :show-reciter="false"></track-card>
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
      <h5 class="section__title">Albums</h5>
      <template v-if="albums">
        <template v-if="albums.length > 0">
          <template v-for="album in albums">
            <album :album="album" :reciter="reciter" :show-reciter="false" v-bind:key="album.id" />
          </template>
          <v-pagination v-model="page" :length="length" circle @input="goToPage"></v-pagination>
        </template>
        <template v-else>
          <p>We don't have any albums for {{ reciter.name }} yet.</p>
        </template>
      </template>
      <template v-else>
        <album-skeleton />
      </template>
    </v-container>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator';
import TrackCard from '@/components/TrackCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import AlbumSkeleton from '@/components/loaders/AlbumSkeleton.vue';
import TrackCardSkeleton from '@/components/loaders/TrackCardSkeleton.vue';
import Album from '@/components/Album.vue';
import HeroBanner from '@/components/HeroBanner.vue';
import { getReciter } from '@/services/reciters';
import { getAlbums } from '@/services/albums';
import { getPopularTracks } from '@/services/popular';


@Component({
  components: {
    HeroBanner,
    TrackCard,
    Album,
    SkeletonCardGrid,
    TrackCardSkeleton,
    AlbumSkeleton,
  },
})
export default class ReciterProfile extends Vue {
  private page = 1;
  private reciter: any = null;
  private albums: any = null;
  private length = 0;
  private popularTracks: any = null;

  mounted() {
    this.fetchData();
  }

  get image() {
    return this.reciter.avatar || '/img/default-reciter-avatar.png';
  }

  get heroAvatarSize() {
    if (this.$vuetify.breakpoint.smAndDown) {
      return 88;
    }

    return 128;
  }

  get showToolbar() {
    return false;
  }

  @Watch('$route')
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
    getAlbums(reciter, { include: 'tracks.media,tracks.reciter,tracks.album,tracks.related' }).then((response) => {
      this.setAlbums(response);
    });
  }

  setAlbums(albums) {
    this.albums = albums.data.data;
    this.length = albums.data.meta.pagination.total_pages;
  }

  goToPage(number) {
    this.albums = null;
    getAlbums(this.reciter.id, {
      include: 'tracks.media,tracks.reciter,tracks.album,tracks.related',
      page: number,
    }).then((response) => {
      this.setAlbums(response);
    });
  }
}
</script>

<style lang="scss" scoped>
@import '../../../styles/theme';

.title {
  margin-bottom: 12px;
}

.v-pagination {
  margin-top: 16px;
}

.reciter-profile__hero {
  position: relative;
  margin-bottom: 36px;

  &--with-toolbar {
    .hero__details {
      margin-bottom: 48px;
    }
  }

  .hero__content {
    padding: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    min-height: 112px;
    flex-direction: column;
  }

  .hero__details {
    margin-top: 24px;
    padding: 0 24px;
  }

  .hero__title {
    font-size: 1.6rem;
    font-weight: 300;
    text-align: center;
  }

  .hero__avatar__component {
    border: 4px solid white;
    @include elevation(4);
  }

  .hero__bar {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    text-align: center;
    padding: 12px 24px;
    background: map-deep-get($colors, 'deep-orange', 'darken-4');

    .bar__content {
      padding: 0;
      display: flex;
      justify-content: space-between;
    }
  }
}

@media #{map-get($display-breakpoints, 'md-and-down')} {
  .reciter-profile__hero {
    margin-bottom: 24px;

    .hero__details {
      margin-top: 16px;
      padding: 0 16px;
    }

    &--with-toolbar {
      .hero__details {
        margin-bottom: 48px;
      }
    }
  }
}
</style>

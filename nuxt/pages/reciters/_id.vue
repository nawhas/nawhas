<template>
  <div class="reciter-profile">
    <hero-banner
      :class="{
        'reciter-profile__hero': true,
        'reciter-profile__hero--with-toolbar': showToolbar
      }"
      background="/backgrounds/azadari-flags.jpg"
    >
      <div class="hero__content">
        <template v-if="reciter">
          <div class="hero__avatar">
            <v-avatar class="hero__avatar__component" :size="heroAvatarSize">
              <img :src="image" :alt="reciter ? reciter.name : 'Loading'">
            </v-avatar>
          </div>
          <div class="hero__details">
            <div v-if="reciter" class="hero__title">
              {{ reciter.name }}
            </div>
            <div v-else class="hero__title" />
          </div>
        </template>
        <v-skeleton-loader v-else type="text" dark width="150px" />
      </div>
      <div v-if="showToolbar" class="hero__bar">
        <v-container class="bar__content">
          <div class="bar__actions bar__actions--visible">
            <v-btn dark text>
              <v-icon left>
                public
              </v-icon> Website
            </v-btn>
            <v-btn dark text>
              <v-icon left>
                star_outline
              </v-icon> Favorite
            </v-btn>
          </div>
          <div class="bar__actions bar__actions--overflow">
            <!--            <edit-reciter-dialog v-if="reciter && isModerator" :reciter="reciter" />-->
            <v-btn dark icon>
              <v-icon>more_vert</v-icon>
            </v-btn>
          </div>
        </v-container>
      </div>
    </hero-banner>
    <v-container class="app__section">
      <h5 class="section__title">
        Top Nawhas
      </h5>
      <template v-if="popularTracks">
        <v-row :dense="$vuetify.breakpoint.smAndDown">
          <v-col v-for="track in popularTracks" :key="track.id" cols="12" sm="6" md="4">
            <track-card v-bind="track" :colored="true" :show-reciter="false" />
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
      <h5 class="section__title section__title--with-actions">
        <span class="d-block">Albums</span>
        <!--        <edit-album-dialog v-if="reciter && isModerator" :reciter="reciter" />-->
      </h5>
      <template v-if="albums">
        <template v-if="albums.length > 0">
          <template v-for="album in albums">
            <album :key="album.id" :album="album" :reciter="reciter" :show-reciter="false" />
          </template>
          <v-pagination
            v-model="page"
            class="album__pagination"
            color="deep-orange"
            :length="length"
            circle
            next-icon="navigate_next"
            prev-icon="navigate_before"
            @input="onPageChanged"
          />
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
import Vue from 'vue';
import { mapGetters } from 'vuex';
import TrackCard from '@/components/tracks/TrackCard.vue';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid.vue';
import AlbumSkeleton from '@/components/loaders/AlbumSkeleton.vue';
import TrackCardSkeleton from '@/components/loaders/TrackCardSkeleton.vue';
import HeroBanner from '@/components/HeroBanner.vue';
import { Reciter } from '@/api/reciters';
import { MetaInfo } from 'vue-meta';
import Album from '@/components/albums/Album.vue';
import { TrackIncludes } from '@/api/tracks';
// import EditReciterDialog from '@/components/edit/EditReciterDialog.vue';
// import EditAlbumDialog from '@/components/edit/EditAlbumDialog.vue';

interface Data {
  page: number;
  length: number;
  reciter: Reciter | null;
  albums: Array<any> | null;
  popularTracks: Array<any> | null;
}

export default Vue.extend({
  components: {
    HeroBanner,
    TrackCard,
    Album,
    SkeletonCardGrid,
    TrackCardSkeleton,
    AlbumSkeleton,
    // EditReciterDialog,
    // EditAlbumDialog,
  },

  async fetch() {
    const { id } = this.$route.params;
    const [reciter, tracks, albums] = await Promise.all([
      this.$api.reciters.get(id),
      this.$api.tracks.popular({
        pagination: { limit: 6 },
        reciterId: id,
        include: [TrackIncludes.Album, TrackIncludes.Reciter],
      }),
      this.$api.albums.index(id, {
        pagination: { page: this.page },
        include: ['tracks.media', 'tracks.reciter', 'tracks.album', 'tracks.related'],
      }),
    ]);

    this.reciter = reciter;
    this.popularTracks = tracks.data;
    this.albums = albums.data;
    this.length = albums.meta.pagination.total_pages;
  },

  data(): Data {
    const page = Number(this.$route.query.page || 1);

    return {
      page,
      length: 1,
      reciter: null,
      albums: null,
      popularTracks: null,
    };
  },

  computed: {
    ...mapGetters('auth', ['isModerator']),
    image(): string {
      return this.reciter?.avatar ?? '/img/default-reciter-avatar.png';
    },
    heroAvatarSize(): number {
      return (this.$vuetify.breakpoint.smAndDown) ? 88 : 128;
    },
    showToolbar(): boolean {
      return Boolean(this.isModerator);
    },
  },

  watch: {
    $route: 'onRouteChanged',
  },

  methods: {
    onRouteChanged() {
      this.$vuetify.goTo(0);
      this.$fetch();
    },
    onPageChanged(page) {
      this.$router.push({ query: { page: String(page) } });
    },
  },

  head(): MetaInfo {
    const title = this.reciter?.name ?? 'Reciter';

    return {
      title,
    };
  },
});
</script>

<style lang="scss" scoped>
@import '~assets/theme';

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

.album__pagination {
  margin-top: 36px;
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

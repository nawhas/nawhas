<template>
  <div>
    <div class="hero" :style="{'background-color': background, color: textColor}">
      <v-container class="hero__content">
        <v-avatar :size="heroArtworkSize" class="hero__artwork" tile>
          <img v-if="album" crossorigin :src="image" :alt="album.name" />
        </v-avatar>
        <div class="hero__text">
          <h4 class="hero__title">
            <template v-if="album">{{ album.title }}</template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="220px" class="py-2 mt-md-2"></v-skeleton-loader>
            </template>
          </h4>
          <div class="hero__meta">
            <template v-if="reciter && album">
              <router-link class="meta__line"
                           :to="{ name: 'reciters.show', params: { reciter: reciter.slug } }"
                           exact
              >
                <span class="meta__line__text">
                  <v-icon class="meta__line__icon">album</v-icon>
                  Album &bull; {{ reciter.name }} &bull; {{ album.year }}
                </span>
              </router-link>
              <div class="meta__line">
                <span class="meta__line__text">
                  <v-icon class="meta__line__icon">playlist_play</v-icon>
                  {{ album.related.tracks | pluralize('track', 'tracks') }}
                </span>
              </div>
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="150px" class="my-2"></v-skeleton-loader>
              <v-skeleton-loader type="text" dark width="150px" class="my-2"></v-skeleton-loader>
            </template>
          </div>
        </div>
      </v-container>
      <div class="hero__bar">
        <v-container class="bar__content">
          <div class="bar__actions bar__actions--visible">
            <template v-if="hasPlayableTracks">
              <v-btn text
                     :color="this.textColor"
                     v-if="tracks"
                     @click="playAlbum"
              >
                <v-icon left>play_circle_filled</v-icon> Play Album
              </v-btn>
              <v-btn text
                     :color="this.textColor"
                     v-if="!addedToQueueSnackbar && tracks"
                     @click="addToQueue"
              >
                <v-icon left>playlist_add</v-icon> Add to Queue
              </v-btn>
              <v-btn text
                     :color="this.textColor"
                     v-if="addedToQueueSnackbar"
              >
                <v-icon color="green" left>done</v-icon> Added to Queue
              </v-btn>
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="100px" class="mt-3"></v-skeleton-loader>
            </template>
          </div>
          <div class="bar__actions bar__actions--overflow">
            <edit-album-dialog v-if="album && isModerator" :album="album"></edit-album-dialog>
            <v-btn dark icon v-if="false"><v-icon>more_vert</v-icon></v-btn>
          </div>
        </v-container>
      </div>
    </div>
    <v-container class="app__section mt-6 pb-12">
      <div class="section__title section__title--with-actions mb-5">
        <div>Tracks</div>
        <edit-track-dialog v-if="album && isModerator" :album="album" />
      </div>
      <v-list class="pa-0" v-if="tracks">
        <v-list-item v-for="(track, index) in tracks" :key="index"
                     :to="getTrackLink(track)"
        >
          <v-list-item-avatar>
            <v-avatar size="36">
              <span>{{ index+1 }}</span>
            </v-avatar>
          </v-list-item-avatar>
          <v-list-item-content>{{ track.title }}</v-list-item-content>
          <v-list-item-action class="track__features">
            <v-icon :class="{
              'material-icons-outlined': true,
              track__feature: true,
              'track__feature--disabled': !hasLyrics(track) && !isDark,
              'track__feature--disabled--dark': !hasLyrics(track) && isDark
            }">
              <template v-if="hasLyrics(track)">speaker_notes</template>
              <template v-else>speaker_notes_off</template>
            </v-icon>
            <v-btn icon @click.prevent="playTrack(track)"
                   :disabled="!hasAudioFile(track)"
                   :class="{
                      track__feature: true,
                      'track__feature--disabled': !hasAudioFile(track) && !isDark,
                      'track__feature--disabled--dark': !hasAudioFile(track) && isDark
                   }"
            >
              <v-icon>
                <template v-if="hasAudioFile(track)">play_circle_outline</template>
                <template v-else>volume_off</template>
              </v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
      </v-list>
      <v-list v-else>
        <v-skeleton-loader type="list-item-avatar"
                           class="py-1"
                           v-for="index in (album ? album.related.tracks : 6)"
                           :key="index">
        </v-skeleton-loader>
      </v-list>
    </v-container>
    <v-snackbar v-model="addedToQueueSnackbar" right>
      <v-icon color="white">playlist_add_check</v-icon> Added to Queue
      <v-btn color="primary" text @click="addedToQueueSnackbar = false">
        Close
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script lang="ts">
/* eslint-disable dot-notation */
import {
  Component, Watch, Prop, Vue,
} from 'vue-property-decorator';
import Vibrant from 'node-vibrant';
import ReciterHeroSkeleton from '@/components/loaders/ReciterHeroSkeleton.vue';
import LyricsSkeleton from '@/components/loaders/LyricsSkeleton.vue';
import MoreTracksSkeleton from '@/components/loaders/MoreTracksSkeleton.vue';
import EditAlbumDialog from '@/components/edit/EditAlbumDialog.vue';
import EditTrackDialog from '@/components/edit/EditTrackDialog.vue';
import { getTracks } from '@/services/tracks';
import { getAlbum } from '@/services/albums';

@Component({
  components: {
    ReciterHeroSkeleton,
    LyricsSkeleton,
    MoreTracksSkeleton,
    EditAlbumDialog,
    EditTrackDialog,
  },
})
export default class AlbumPage extends Vue {
  @Prop({ type: Object }) private albumObject!: any;
  private background = 'rgb(47,47,47)';
  private textColor = '#fff';
  private track: any = null;
  private album: any = null;
  private tracks: any = null;
  private addedToQueueSnackbar = false;

  get reciter() {
    return this.album && this.album.reciter;
  }

  get image() {
    if (this.album) {
      return this.album.artwork || '/img/default-album-image.png';
    }
    return '/img/default-album-image.png';
  }

  get playable() {
    if (!this.tracks) {
      return [];
    }

    return this.tracks.filter((track) => this.hasAudioFile(track));
  }

  get hasPlayableTracks(): boolean {
    return this.playable.length > 0;
  }

  get heroArtworkSize() {
    if (this.$vuetify.breakpoint.xsOnly) {
      return 96;
    }
    if (this.$vuetify.breakpoint.smOnly) {
      return 128;
    }
    return 192;
  }

  get isModerator() {
    return this.$store.getters['auth/isModerator'];
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }

  mounted() {
    this.fetchData();
  }

  @Watch('$route')
  onRouteUpdate() {
    this.fetchData();
  }

  async fetchData() {
    this.$Progress.start();
    const { reciter, album } = this.$route.params;

    if (this.albumObject) {
      this.album = this.albumObject;
    }

    if (!this.album || !this.isSameAlbum((this.$route.params as any))) {
      await getAlbum(reciter, album, {
        include: 'reciter,related',
      }).then((r) => {
        this.album = r.data;
      });
    }

    await getTracks(reciter, album, {
      include: 'reciter,lyrics,album,media,related',
    }).then((r) => {
      this.tracks = r.data.data;
    });

    this.setBackgroundFromImage();
    this.$Progress.finish();
  }

  setBackgroundFromImage() {
    if (!this.album) {
      return;
    }
    Vibrant.from(this.image)
      .getPalette()
      .then((palette) => {
        const swatch = palette.DarkMuted;
        if (!swatch) {
          return;
        }
        this.background = swatch.getHex();
        this.textColor = swatch.getBodyTextColor();
      });
  }

  isSameAlbum({ reciter, album }) {
    return (
      this.track.reciter.slug === reciter
      && this.track.album.year === album
    );
  }

  getTrackLink(track) {
    return {
      name: 'tracks.show',
      params: {
        reciter: this.reciter.slug,
        album: this.album.year,
        track: track.slug,
      },
    };
  }

  hasAudioFile(track) {
    return track.related ? track.related.audio === true : false;
  }

  hasLyrics(track) {
    return track.related ? track.related.lyrics === true : false;
  }

  playAlbum() {
    this.$store.commit('player/PLAY_ALBUM', { tracks: this.playable, start: this.playable[0] });
  }

  playTrack(track) {
    this.$store.commit('player/PLAY_ALBUM', { tracks: this.playable, start: track });
  }

  addToQueue() {
    this.$store.commit('player/ADD_ALBUM_TO_QUEUE', { tracks: this.playable });
    this.addedToQueueSnackbar = true;
  }
}
</script>

<style lang="scss" scoped>
@import "../../../styles/theme";

.hero {
  width: 100%;
  position: relative;
  padding-bottom: 60px;
}

.hero__artwork {
  border: 4px solid white;
  border-radius: 4px;
  overflow: hidden;
  margin-right: 48px;
  box-sizing: content-box;
  background-color: white;
}

.hero__title {
  font-family: 'Roboto Slab', sans-serif;
  font-weight: bold;
  font-size: 2.4rem;
}

.hero__meta {
  display: flex;
  flex-direction: column;
  align-items: flex-start;

  .meta__line {
    font-weight: 400;
    padding: 4px 0;
    text-decoration: none;
    color: inherit;
    display: block;
  }
  .meta__line__text {
    display: flex;
    align-items: center;
    justify-content: flex-start;
  }
  .meta__line__icon {
    margin-right: 8px;
  }
}

.hero__content {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-start;
  padding-top: 36px;
  padding-bottom: 36px;
}

.hero__bar {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  text-align: center;
  padding: 12px 24px;
  background: rgba(0, 0, 0, 0.2);

  .bar__content {
    padding: 0;
    display: flex;
    justify-content: space-between;
  }
}

.track__features {
  white-space: nowrap;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  flex-direction: row;

  .track__feature {
    margin-left: 6px;
    color: map-deep-get($colors, 'deep-orange', 'darken-3');
  }

  .track__feature--disabled {
    color: rgba(0, 0, 0, 0.1);
  }
  .track__feature--disabled--dark {
    color: rgba(map-get($shades, 'white'), 0.5);
  }
}

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .hero__content {
    padding: 32px;
  }
  .hero__title {
    font-size: 1.9rem;
  }
  .hero__artwork {
    margin-right: 24px;
  }
  .hero__meta .meta__line {
    padding: 3px 0;
  }
}

@media #{map-get($display-breakpoints, 'xs-only')} {
  .hero__title {
    font-size: 1.4rem;
  }
  .hero__meta .meta__line {
    padding: 2px 0;
  }
}
</style>

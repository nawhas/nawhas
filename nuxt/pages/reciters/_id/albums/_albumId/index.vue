<template>
  <div>
    <div class="hero" :style="{'background-color': background, color: textColor}">
      <v-container class="hero__content">
        <v-avatar :size="heroArtworkSize" class="hero__artwork" tile>
          <img v-if="album" crossorigin :src="image" :alt="album.name">
        </v-avatar>
        <div class="hero__text">
          <h4 class="hero__title">
            <template v-if="album">
              {{ album.title }}
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="220px" class="py-2 mt-md-2" />
            </template>
          </h4>
          <div class="hero__meta">
            <template v-if="reciter && album">
              <router-link
                class="meta__line"
                :to="getReciterUri(reciter)"
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
              <v-skeleton-loader type="text" dark width="150px" class="my-2" />
              <v-skeleton-loader type="text" dark width="150px" class="my-2" />
            </template>
          </div>
        </div>
      </v-container>
      <div class="hero__bar">
        <v-container class="bar__content">
          <div class="bar__actions bar__actions--visible">
            <template v-if="hasPlayableTracks">
              <v-btn
                v-if="tracks"
                text
                :color="textColor"
                @click="playAlbum"
              >
                <v-icon left>
                  play_circle_filled
                </v-icon>
                Play Album
              </v-btn>
              <v-btn
                v-if="!addedToQueueSnackbar && tracks"
                text
                :color="textColor"
                @click="addToQueue"
              >
                <v-icon left>
                  playlist_add
                </v-icon>
                Add to Queue
              </v-btn>
              <v-btn
                v-if="addedToQueueSnackbar"
                text
                :color="textColor"
              >
                <v-icon color="green" left>
                  done
                </v-icon>
                Added to Queue
              </v-btn>
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="100px" class="mt-3" />
            </template>
          </div>
          <div class="bar__actions bar__actions--overflow">
            <edit-album-dialog v-if="album && isModerator" :album="album" />
            <v-btn v-if="false" dark icon>
              <v-icon>more_vert</v-icon>
            </v-btn>
          </div>
        </v-container>
      </div>
    </div>
    <v-container class="app__section mt-6 pb-12">
      <div class="section__title section__title--with-actions mb-5">
        <div>Tracks</div>
        <edit-track-dialog v-if="album && isModerator" :album="album" />
      </div>
      <track-list :tracks="tracks" />
    </v-container>
    <v-snackbar v-model="addedToQueueSnackbar" right>
      <v-icon color="white">
        playlist_add_check
      </v-icon>
      Added to Queue
      <v-btn color="primary" text @click="addedToQueueSnackbar = false">
        Close
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Vibrant from 'node-vibrant';
import { mapGetters } from 'vuex';
import { MetaInfo } from 'vue-meta';
import { AlbumIncludes } from '@/api/albums';
import { TrackIncludes } from '@/api/tracks';
import { Album, getAlbumArtwork } from '@/entities/album';
import { Track, getTrackUri } from '@/entities/track';
import { Reciter, getReciterUri } from '@/entities/reciter';
import TrackList from '@/components/tracks/TrackList.vue';
import EditTrackDialog from '@/components/edit/EditTrackDialog.vue';
import EditAlbumDialog from '@/components/edit/EditAlbumDialog.vue';

interface Data {
  album: Album | null;
  background: string | null;
  textColor: string | null;
  track: Track | null;
  tracks: Array<Track> | null;
  addedToQueueSnackbar: boolean;
}

export default Vue.extend({
  components: {
    TrackList,
    EditTrackDialog,
    EditAlbumDialog,
  },
  async fetch() {
    const { id, albumId } = this.$route.params;
    this.album = await this.$api.albums.get(id, albumId, {
      include: [AlbumIncludes.Reciter, AlbumIncludes.Related],
    });

    const response = await this.$api.tracks.index(id, albumId, {
      include: [
        TrackIncludes.Reciter,
        TrackIncludes.Lyrics,
        TrackIncludes.Album,
        TrackIncludes.Media,
        TrackIncludes.Related,
      ],
    });

    this.tracks = response.data;

    this.setBackgroundFromImage();
  },

  data(): Data {
    return {
      album: null,
      background: 'rgb(47,47,47)',
      textColor: '#fff',
      track: null,
      tracks: null,
      addedToQueueSnackbar: false,
    };
  },

  computed: {
    ...mapGetters('auth', ['isModerator']),
    reciter(): Reciter | null {
      return this.album?.reciter ?? null;
    },
    image(): string {
      return getAlbumArtwork(this.album);
    },
    playable(): Array<any> {
      if (!this.tracks) {
        return [];
      }

      return this.tracks.filter((track) => this.hasAudioFile(track));
    },
    hasPlayableTracks(): boolean {
      return this.playable.length > 0;
    },
    heroArtworkSize(): number {
      if (this.$vuetify.breakpoint.xsOnly) {
        return 96;
      }
      if (this.$vuetify.breakpoint.smOnly) {
        return 128;
      }
      return 192;
    },
    isDark(): boolean {
      return this.$vuetify.theme.dark;
    },
  },

  watch: {
    $route: 'onRouteChanged',
  },

  methods: {
    getReciterUri,
    getTrackUri,
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
    },
    isSameAlbum({ reciter, album }): boolean {
      if (!this.track || this.track.reciter === undefined || this.track.album === undefined) {
        return false;
      }
      return (
        this.track.reciter.slug === reciter &&
        this.track.album.year === album
      );
    },
    hasAudioFile(track): boolean {
      return track.related?.audio ?? false;
    },
    hasLyrics(track): boolean {
      return track.related?.lyrics ?? false;
    },
    playAlbum() {
      this.$store.commit('player/PLAY_ALBUM', { tracks: this.playable, start: this.playable[0] });
    },
    playTrack(track) {
      this.$store.commit('player/PLAY_ALBUM', { tracks: this.playable, start: track });
    },
    addToQueue() {
      this.$store.commit('player/ADD_ALBUM_TO_QUEUE', { tracks: this.playable });
      this.addedToQueueSnackbar = true;
    },
    onRouteChanged() {
      this.$vuetify.goTo(0);
      this.$fetch();
    },
  },

  head(): MetaInfo {
    let title = `${this.$route.params.albumId} Album`;

    if (this.album) {
      title = `${this.album.title} (${this.album.year}) - Album by ${this.album.reciter?.name}`;
    }

    return { title };
  },
});
</script>

<style lang="scss" scoped>
@import "~assets/theme";

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

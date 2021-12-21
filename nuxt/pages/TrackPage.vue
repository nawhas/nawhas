<router>
  path: /reciters/:reciter/albums/:album/tracks/:track
  name: "tracks.show"
</router>

<template>
  <div :class="{ 'track-page': true, 'track-page--dark': isDark }">
    <div class="hero" :style="{'background-color': background, color: textColor}">
      <v-container class="hero__content">
        <v-avatar :size="heroArtworkSize" class="hero__artwork" tile>
          <lazy-image v-if="track && album" crossorigin :src="image" :alt="album.name" />
        </v-avatar>
        <div class="hero__text">
          <h4 class="hero__title">
            <template v-if="track">
              {{ track.title }}
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="220px" class="py-md-8 py-2" />
            </template>
          </h4>
          <div class="hero__meta">
            <template v-if="reciter && album">
              <router-link
                class="meta__line"
                :to="getReciterUri(reciter)"
                exact
              >
                <span class="meta__line__text">{{ reciter.name }}</span>
              </router-link>
              <br>
              <router-link class="meta__line" :to="getAlbumUri(album, reciter)" exact>
                <span class="meta__line__text">{{ album.year }} &bull; {{ album.title }}</span>
              </router-link>
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="150px" class="my-2" />
              <v-skeleton-loader type="text" dark width="100px" class="my-2" />
            </template>
          </div>
        </div>
      </v-container>
      <div class="hero__bar">
        <v-container class="bar__content">
          <div class="bar__actions bar__actions--visible">
            <template v-if="track && albumTracks">
              <v-btn
                v-if="hasAudio && albumTracks && !isSameTrackPlaying"
                :text="showExpandedButtonText"
                :icon="!showExpandedButtonText"
                :color="textColor"
                @click="playAlbum"
              >
                <v-icon :left="showExpandedButtonText">
                  play_circle_filled
                </v-icon>
                <span v-if="showExpandedButtonText">Play</span>
              </v-btn>
              <v-btn
                v-else-if="hasAudio && isSameTrackPlaying"
                :text="showExpandedButtonText"
                :icon="!showExpandedButtonText"
                :color="textColor"
                @click="stopPlaying"
              >
                <v-icon :left="showExpandedButtonText">
                  stop
                </v-icon>
                <span v-if="showExpandedButtonText">Stop</span>
              </v-btn>
              <v-btn
                v-if="hasAudio && !addedToQueueSnackbar && albumTracks"
                :text="showExpandedButtonText"
                :icon="!showExpandedButtonText"
                :color="textColor"
                @click="addToQueue"
              >
                <v-icon :left="showExpandedButtonText">
                  playlist_add
                </v-icon>
                <span v-if="showExpandedButtonText">Add to Queue</span>
              </v-btn>
              <v-btn
                v-if="hasAudio && addedToQueueSnackbar"
                :text="showExpandedButtonText"
                :icon="!showExpandedButtonText"
                :color="textColor"
              >
                <v-icon
                  color="green"
                  :left="showExpandedButtonText"
                >
                  playlist_add_check
                </v-icon>
                <span v-if="showExpandedButtonText">Added to Queue</span>
              </v-btn>
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="100px" class="mt-3" />
            </template>
          </div>
          <div class="bar__actions bar__actions--overflow">
            <favorite-track-button v-if="track" :track="track.id" />
            <v-btn v-if="track && track.lyrics" icon :color="textColor" @click="print">
              <v-icon>print</v-icon>
            </v-btn>
            <template v-if="track && isModerator">
              <edit-track-dialog :track="track" />
            </template>
            <v-btn v-if="false" dark icon>
              <v-icon>more_vert</v-icon>
            </v-btn>
          </div>
        </v-container>
      </div>
    </div>

    <v-container class="app__section">
      <v-row>
        <v-col cols="12" md="8">
          <lyrics-card :track="track" />
        </v-col>
        <v-col cols="12" md="4">
          <v-card v-if="video" class="card card--video">
            <v-card-title
              class="card__title subtitle-1"
            >
              <v-icon class="card__title__icon">
                ondemand_video
              </v-icon>
              <div>Video</div>
            </v-card-title>
            <v-card-text class="pa-0">
              <client-only>
                <youtube class="youtube" :video-id="video" />
              </client-only>
            </v-card-text>
          </v-card>
          <v-card class="card card--album">
            <v-card-title
              class="card__title subtitle-1 card__title--link"
              @click="$router.push(getAlbumUri(album, reciter))"
            >
              <v-icon class="card__title__icon">
                format_list_bulleted
              </v-icon>
              <div>More From This Album</div>
            </v-card-title>
            <v-card-text v-if="track && albumTracks" class="pa-0">
              <nuxt-link
                v-for="(albumTrack, index) in album.tracks.data"
                :key="albumTrack.id"
                class="album__track"
                :exact="true"
                active-class="album__track--active"
                tag="a"
                :to="getTrackUri(albumTrack, reciter)"
              >
                <v-avatar class="album__track__avatar" size="28">
                  <span>{{ index+1 }}</span>
                </v-avatar>
                <div class="album__track__text">
                  {{ albumTrack.title }}
                </div>
              </nuxt-link>
            </v-card-text>
            <v-card-text v-else>
              <more-tracks-skeleton />
            </v-card-text>
            <v-card-actions v-if="album && isModerator" class="d-flex justify-end album__actions">
              <edit-track-dialog :album="album" />
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-snackbar v-model="addedToQueueSnackbar" right>
      <v-icon color="white">
        playlist_add_check
      </v-icon>Added to Queue
      <v-btn color="deep-orange" text @click="undo">
        Undo
      </v-btn>
      <v-btn color="green" text @click="addedToQueueSnackbar = false">
        Close
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script lang="ts">
/* eslint-disable dot-notation */
import Vue from 'vue';
import { mapGetters } from 'vuex';
import getYouTubeID from 'get-youtube-id';
import Vibrant from 'node-vibrant';
import { MetaInfo } from 'vue-meta';
import MoreTracksSkeleton from '@/components/loaders/MoreTracksSkeleton.vue';
import EditTrackDialog from '@/components/edit/EditTrackDialog.vue';
import LyricsCard from '@/components/lyrics/LyricsCard.vue';
import { Track, getTrackUri } from '@/entities/track';
import { Reciter, getReciterUri } from '@/entities/reciter';
import { Album, getAlbumArtwork, getAlbumUri } from '@/entities/album';
import { TrackIncludes } from '@/api/tracks';
import { generateMeta } from '@/utils/meta';
import LazyImage from '@/components/utils/LazyImage.vue';
import FavoriteTrackButton from '@/components/tracks/FavoriteTrackButton.vue';

interface Data {
  track: Track | null;
  background: string;
  textColor: string;
  addedToQueueSnackbar: boolean;
}

export default Vue.extend({
  components: {
    FavoriteTrackButton,
    LazyImage,
    MoreTracksSkeleton,
    EditTrackDialog,
    LyricsCard,
  },

  async asyncData({ route, $api, $errors }) {
    const { reciter: reciterId, album: albumId, track: trackId } = route.params;

    try {
      const track = await $api.tracks.get(reciterId, albumId, trackId, {
        include: [
          TrackIncludes.Reciter,
          TrackIncludes.Lyrics,
          TrackIncludes.Media,
          'album.tracks',
          'album.tracks.reciter',
          'album.tracks.lyrics',
          'album.tracks.media',
          'album.tracks.album',
          'album.tracks.related',
        ],
      });

      return { track };
    } catch (e) {
      await $errors.handle404();
    }
  },

  data: (): Data => ({
    track: null,
    background: 'rgb(150, 37, 2)',
    textColor: '#fff',
    addedToQueueSnackbar: false,
  }),

  head(): MetaInfo {
    let title = 'Loading...';
    let description;
    const image = getAlbumArtwork(this.track?.album);

    if (this.track) {
      title = `${this.track.title} (${this.track.year}) - Nawha by ${this.track.reciter?.name}`;

      description = `${this.track.title} is a nawha by ${this.track.reciter?.name}, released in ${this.track.year} ` +
        `as part of their album titled ${this.track.album?.title}.`;
    }

    return generateMeta({
      title,
      description,
      image,
    });
  },

  computed: {
    ...mapGetters('auth', ['isModerator']),
    video(): string|null {
      if (!this.track || !this.track.video) {
        return null;
      }

      return getYouTubeID(this.track.video);
    },
    showExpandedButtonText(): boolean {
      return this.$vuetify.breakpoint.mdAndUp;
    },
    reciter(): Reciter|null {
      return this.track?.reciter ?? null;
    },
    album(): Album|null {
      return this.track?.album ?? null;
    },
    image(): string {
      return getAlbumArtwork(this.track?.album);
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
    hasAudio(): boolean {
      if (this.track?.media === undefined) {
        return false;
      }
      return this.track.media.data.length > 0;
    },
    isSameTrackPlaying(): boolean {
      const current = this.$store.getters['player/track'];
      if (!current || !this.track) {
        return false;
      }
      return (this.track.id === current.track.id);
    },
    isInQueue(): boolean {
      if (!this.track) {
        return false;
      }

      const { player } = this.$store.state;
      for (let index = 0; index < player.queue.length; index++) {
        const element = player.queue[index];
        if (element.track.id === this.track.id) {
          return true;
        }
      }

      return false;
    },
    isDark(): boolean {
      return this.$vuetify.theme.dark;
    },
    isTrackSaved(): boolean {
      return this.$store.getters['library/isTrackSaved'](this.track?.id);
    },
    savedTextColor(): string {
      return (this.isTrackSaved) ? '#FE5B00' : this.textColor;
    },
    albumTracks(): Array<Track> | null {
      if (!this.track) {
        return null;
      }

      return this.track.album?.tracks?.data ?? null;
    },
  },

  mounted() {
    const handler = (e) => {
      e.preventDefault();
      this.print();
    };
    this.$el['__onPrintHandler__'] = handler;
    window.addEventListener('beforeprint', handler);

    this.setBackgroundFromImage();
  },

  beforeDestroy() {
    window.removeEventListener('beforeprint', this.$el['__onPrintHandler__']);
    delete this.$el['__onPrintHandler__'];
  },

  methods: {
    getReciterUri,
    getAlbumUri,
    getTrackUri,
    setBackgroundFromImage() {
      if (!this.track) {
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

    print() {
      if (this.reciter && this.album && this.track) {
        const url = `/print/${this.reciter.slug}/${this.album.year}/${this.track.slug}`;
        window.open(url, '_blank', 'location=yes,height=570,width=1024,scrollbars=yes,status=yes');
      }
    },

    playAlbum() {
      this.$store.commit('player/PLAY_ALBUM', {
        tracks: this.albumTracks,
        start: this.track,
      });
    },

    stopPlaying() {
      this.$store.commit('player/STOP');
    },

    addToQueue() {
      this.$store.commit('player/ADD_TO_QUEUE', { track: this.track });
      this.addedToQueueSnackbar = true;
    },

    undo() {
      const { id } = this.$store.state.player.queue.slice(-1)[0];
      this.$store.commit('player/REMOVE_TRACK', { id });
      this.addedToQueueSnackbar = false;
    },
  },
});
</script>

<style lang="scss" scoped>
@import '~assets/theme';
@import '~assets/tracks/cards';

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
  .meta__line {
    font-weight: 400;
    padding: 4px 0;
    text-decoration: none;
    color: inherit;
    display: inline-block;
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

.card--video {
  margin-bottom: 24px;
}

.card--album {
  padding: 0;
  margin-bottom: 12px;

  .album__track {
    text-decoration: none;
    color: inherit;
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    will-change: background-color;
    transition: background-color 0.2s;
    background-color: transparent;

    &:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }

    .album__track__avatar {
      font-family: 'Roboto Slab', sans-serif;
      margin-right: 15px;
      display: flex;
      align-items: center;
    }

    .album__track__text {
      font-size: 14px;
    }
  }

  .album__track--active {
    background-color: rgba(0, 0, 0, 0.1);

    .album__track__avatar {
      color: white;
      background-color: map-deep-get($colors, 'deep-orange', 'darken-1');
    }
    .album__track__text {
      font-weight: 600;
    }
  }
  .album-tracks__actions {
    background: rgba(0, 0, 0, 0.1);
  }

  .album__actions {
    background-color: rgba(0, 0, 0, 0.1);
  }
}

.track-page--dark {
  .hero__artwork {
    background: #1e1e1e;
    border-color: #1e1e1e;
  }
}

.youtube {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
  max-width: 100%;

  ::v-deep iframe,
  ::v-deep object,
  ::v-deep embed {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
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

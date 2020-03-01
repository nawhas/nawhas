<template>
  <div>
    <template v-if="reciter && album && track">
      <div class="track-hero" :style="{'background-color': background, color: textColor}">
        <div class="track-hero__content">
          <div class="track-hero__left">
            <div class="track-hero__avatar" :elevation="2">
              <v-avatar size="120px" class="white" tile>
                <img :src="image" :alt="album.name" />
              </v-avatar>
            </div>
            <div class="track-hero__text">
              <h4 class="track-hero__title">{{ track.title }}</h4>
              <div class="track-hero__meta">
                <p>{{ reciter.name }}</p>
                <p>{{ album.year }} &bull; {{ album.title }}</p>
              </div>
              <div class="track-hero__audio_buttons" v-if="track.media.data.length > 0">
                <v-btn @click="playTrack" text class="track-hero__audio_buttons--play white--text">
                  <v-icon class="track-hero__audio_buttons--icons">play_circle_filled</v-icon>Play Audio
                </v-btn>
                <v-btn @click="addToQueue" text class="track-hero__audio_buttons--queue white--text">
                  <v-icon class="track-hero__audio_buttons--icons">playlist_add</v-icon>Add to Queue
                </v-btn>
              </div>
            </div>
          </div>
          <div class="track-hero__actions">
            <ul>
              <li>
                <v-btn icon class="white--text">
                  <v-icon>add_to_photos</v-icon>
                </v-btn>
              </li>
              <li>
                <v-btn icon class="white--text">
                  <v-icon>share</v-icon>
                </v-btn>
              </li>
              <li>
                <v-btn @click="print" icon class="white--text">
                  <v-icon>print</v-icon>
                </v-btn>
              </li>
              <li>
                <v-btn icon class="white--text">
                  <v-icon>more_horiz</v-icon>
                </v-btn>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </template>

    <template v-else>
      <reciter-hero-skeleton></reciter-hero-skeleton>
    </template>

    <div class="track-page-content">
      <v-container grid-list-xl>
        <v-layout row>
          <v-flex md7>
            <v-card class="track-page-content__card track-page-content__card--lyrics lyrics">
              <template v-if="track">
                <div class="lyrics__content" v-if="track.lyrics">
                  <div v-html="prepareLyrics(track.lyrics.content)"></div>
                </div>
                <div class="lyrics__empty" v-else>
                  <div class="lyrics__empty-message">We don't have a write-up for this nawha yet.</div>
                </div>
              </template>
              <template v-else>
                <div class="lyrics__content">
                  <lyrics-skeleton />
                </div>
              </template>
            </v-card>
          </v-flex>
          <v-flex md5>
            <v-card class="track-page-content__card track-page-content__card--audio">
              <section>
                <p>There is no video available</p>
              </section>
            </v-card>
            <v-card class="track-page-content__card track-page-content__card--album">
              <v-card-title class="track-page-content__card--album--title subtitle-1">
                <v-icon class="track-page-content__card--album--title--icon">format_list_bulleted</v-icon>
                <span>More From This Album</span>
              </v-card-title>
              <v-card-text class="pa-0 subtitle-1" v-if="track && album">
                <router-link
                  v-for="(albumTrack, index) in album.tracks.data"
                  :key="albumTrack.id"
                  class="album__tracks"
                  tag="div"
                  :to="`/reciters/${reciter.slug}/albums/${album.year}/tracks/${albumTrack.slug}`"
                >
                  <template v-if="track.id === albumTrack.id">
                    <v-avatar class="album__tracks__avatar" color="primary" size="28">
                      <span class="white--text">
                        <strong>{{ index+1 }}</strong>
                      </span>
                    </v-avatar>
                    <div class="album__tracks__text">
                      <strong>{{ albumTrack.title }}</strong>
                    </div>
                  </template>
                  <template v-else>
                    <v-avatar class="album__tracks__avatar" color="grey lighten-4" size="28">
                      <span>{{ index+1 }}</span>
                    </v-avatar>
                    <div class="album__tracks__text">{{ albumTrack.title }}</div>
                  </template>
                </router-link>
              </v-card-text>
              <v-card-text v-else>
                <more-tracks-skeleton />
              </v-card-text>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </div>
    <v-snackbar v-model="addedToQueueSnackbar" right>
      <v-icon color="white">playlist_add_check</v-icon> Added to Queue
      <v-btn color="deep-orange" text @click="addedToQueueSnackbar = false">
        Close
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script>
/* eslint-disable dot-notation */
import Vibrant from 'node-vibrant';
import ReciterHeroSkeleton from '@/components/ReciterHeroSkeleton.vue';
import LyricsSkeleton from '@/components/LyricsSkeleton.vue';
import MoreTracksSkeleton from '@/components/MoreTracksSkeleton.vue';
import { getTrack } from '@/services/tracks';

export default {
  name: 'TrackPage',
  components: {
    ReciterHeroSkeleton,
    LyricsSkeleton,
    MoreTracksSkeleton,
  },
  props: ['trackObject'],

  watch: {
    // call again the method if the route changes
    $route: 'onRouteUpdate',
  },

  data() {
    return {
      background: '#222',
      textColor: '#fff',
      track: null,
      addedToQueueSnackbar: false,
    };
  },

  computed: {
    reciter() {
      return this.track && this.track.reciter;
    },
    album() {
      return this.track && this.track.album;
    },
    image() {
      if (this.album) {
        return this.album.artwork || '/img/default-album-image.png';
      }
      return '/img/default-album-image.png';
    },
  },

  mounted() {
    this.fetchData();
    const handler = (e) => {
      e.preventDefault();
      this.print();
    };
    this.$el['__onPrintHandler__'] = handler;
    window.addEventListener('beforeprint', handler);
  },
  beforeDestroy() {
    window.removeEventListener('beforeprint', this.$el['__onPrintHandler__']);
    delete this.$el['__onPrintHandler__'];
  },
  methods: {
    onRouteUpdate() {
      this.fetchData();
    },
    async fetchData() {
      this.$Progress.start();
      const { reciter, album, track } = this.$route.params;

      if (this.trackObject) {
        this.track = this.trackObject;
      }

      if (!this.track || !this.isSameTrack(this.$route.params)) {
        await getTrack(reciter, album, track, {
          include: 'reciter,lyrics,album.tracks,media',
        }).then((r) => {
          this.track = r.data;
        });
      }

      this.setBackgroundFromImage();
      this.$Progress.finish();
    },
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
    prepareLyrics(content) {
      return content.replace(/\n/gi, '<br>');
    },
    isSameTrack({ reciter, album, track }) {
      return (
        this.track.reciter.slug === reciter
        && this.track.album.year === album
        && this.track.slug === track
      );
    },
    print() {
      this.$router.replace({
        name: 'print.lyrics',
        params: {
          track: this.track.slug,
          reciter: this.reciter.slug,
          album: this.album.year,
          trackObject: this.track,
        },
      });
    },
    playTrack() {
      this.$store.commit('player/PLAY_TRACK', { track: this.track });
    },
    addToQueue() {
      this.$store.commit('player/ADD_TO_QUEUE', { track: this.track });
      this.addedToQueueSnackbar = true;
    },
  },
};
</script>

<style lang="scss" scoped>
.track-hero {
  width: 100%;
  padding-bottom: 80px;

  .track-hero__content {
    max-width: 1000px;
    padding: 48px 24px 24px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;

    .track-hero__left {
      display: flex;
      align-items: center;
    }

    .track-hero__avatar {
      border: 2px solid white;
      border-radius: 4px;
      overflow: hidden;
      margin-right: 24px;
    }

    .track-hero__title {
      font-family: 'Roboto Slab', sans-serif;
      font-weight: bold;
      font-size: 38px;
    }

    .track-hero__meta {
      p {
        font-size: 15px;
        margin: 0 0 6px 0;
        padding: 0;
      }
    }

    .track-hero__audio_buttons {
      &--play {
        padding: 2px;
        margin-right: 10px;
      }

      &--queue {
        padding: 2px;
      }

      &--icons {
        margin-right: 6px;
      }
    }

    .track-hero__actions {
      ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;

        li {
          margin: -2px 0 -2px 0;
          padding: 0;
        }
      }
    }
  }
}

.track-page-content {
  margin-top: -72px;
  padding: 0 8px;
  max-width: 1024px;
  margin-left: auto;
  margin-right: auto;

  .track-page-content__card {
    padding: 24px;
    margin-bottom: 24px;
    min-height: 200px;

    &--album {
      padding: 0px;
      margin-bottom: 12px;

      &--title {
        border-bottom: rgba(0, 0, 0, 0.05) solid 1px;

        &--icon {
          margin-right: 14px;
        }
      }

      .album__tracks {
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        will-change: background-color;
        transition: background-color 0.2s;
        background-color: transparent;

        &:hover {
          background-color: rgba(0, 0, 0, 0.15);
        }

        .album__tracks__avatar {
          font-family: 'Roboto Slab', sans-serif;
          margin-right: 15px;
        }

        .album__tracks__text {
          font-size: 14px;
        }
      }
    }

    &--lyrics {
      min-height: 500px;
      height: auto;
      font-family: 'Roboto Slab', sans-serif;

      .lyrics__empty {
        display: flex;
        justify-content: center;
        color: rgba(0, 0, 0, 0.3);
        font-size: 20px;
        height: 400px;
        font-weight: 300;

        .lyrics__empty-message {
          display: flex;
          margin: auto;
          align-self: center;
        }
      }
    }
  }
}

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

  .reciter-hero__social {
    font-size: 140%;
    list-style: none;
    margin: 16px 0;
    padding: 0;
    text-align: center;

    li {
      display: inline;

      a {
        color: inherit;
        padding: 8px;
        will-change: color;
        &:hover {
        }
      }
    }
  }

  .reciter-hero__bio {
    margin: 16px 0 0 0;
    padding: 0;
    max-height: 108px;
    overflow: hidden;
    position: relative;
  }
}
</style>

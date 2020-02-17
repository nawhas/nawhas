<template>
  <div>
    <template v-if="!loading">
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
                <v-btn icon class="white--text">
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
              <template v-if="!loading">
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
                <p>There is no track available yet</p>
              </section>
            </v-card>
            <v-card class="track-page-content__card track-page-content__card--audio">
              <section>
                <p>There is no video available</p>
              </section>
            </v-card>
            <v-card class="track-page-content__card track-page-content__card--album">More</v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </div>
  </div>
</template>

<script>
import Vibrant from 'node-vibrant';
import ReciterHeroSkeleton from '@/components/ReciterHeroSkeleton.vue';
import LyricsSkeleton from '@/components/LyricsSkeleton.vue';
import { getTrack } from '@/services/tracks';

export default {
  name: 'TrackPage',
  components: {
    ReciterHeroSkeleton,
    LyricsSkeleton,
  },
  data() {
    return {
      background: '#222',
      textColor: '#fff',
      reciter: {},
      album: {},
      track: {},
      loading: false,
    };
  },
  async mounted() {
    this.loading = true;
    const { reciter, album, track } = this.$route.params;
    const [data] = await Promise.all([getTrack(reciter, album, track, { include: 'reciter,album,lyrics' })]);
    this.setData(data);
    this.setBackgroundFromImage();
    this.loading = false;
  },
  computed: {
    image() {
      return this.album.artwork || '/img/default-album-image.png';
    },
  },
  methods: {
    setData(data) {
      this.reciter = data.data.reciter;
      this.album = data.data.album;
      this.track = data.data;
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
    height: 200px;

    &--lyrics {
      min-height: 500px;
      height: auto;

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

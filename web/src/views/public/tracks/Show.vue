<template>
  <div>
    <div class="track-hero" :style="{'background-color': background, color: textColor}">
      <div class="track-hero__content">
        <div class="track-hero__left">
          <div class="track-hero__avatar" :elevation="2">
            <v-avatar size="120px" class="white" tile>
              <img :src="album.artwork" :alt="album.name" />
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

    <div class="track-page-content">
      <v-container grid-list-xl>
        <v-layout row>
          <v-flex md7>
            <v-card class="track-page-content__card track-page-content__card--lyrics lyrics">
              <div class="lyrics__content" v-if="track.lyrics">
                <p v-html="track.lyrics.content"></p>
              </div>
              <div class="lyrics__empty" v-else>
                <div class="lyrics__empty-message">We don't have a write-up for this nawha yet.</div>
              </div>
            </v-card>
          </v-flex>
          <v-flex md5>
            <v-card class="track-page-content__card track-page-content__card--audio">
              Audio
              <section>
                <p>There is no track available yet</p>
              </section>
            </v-card>
            <v-card class="track-page-content__card track-page-content__card--audio">
              <section>
                <p>There is no video available</p>
              </section>
            </v-card>
            <v-card class="track-page-content__card track-page-content__card--album">
              More
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </div>
  </div>
</template>

<script lang="ts">
import { mapGetters } from 'vuex';
import Vibrant from 'node-vibrant';
import store from '@/store';

async function fetchData(reciter, album, track) {
  await Promise.all([
    store.dispatch('reciters/fetchReciter', { reciter }),
    store.dispatch('albums/fetchAlbum', { reciter, album }),
    store.dispatch('tracks/fetchTrack', { reciter, album, track }),
  ]);
}

export default {
  name: 'tracks.show',
  data() {
    return {
      background: '#222',
      textColor: '#fff',
    };
  },
  computed: {
    ...mapGetters({
      reciter: 'reciters/reciter',
      album: 'albums/album',
      track: 'tracks/track',
    }),
  },
  methods: {
    setBackgroundFromImage() {
      if (!this.track) {
        return;
      }
      Vibrant.from(this.album.artwork)
        .getPalette()
        .then(palette => {
          const swatch = palette.DarkMuted;
          if (!swatch) {
            return;
          }
          this.background = swatch.getHex();
          this.textColor = swatch.getBodyTextColor();
        });
    },
  },
  created() {
    this.setBackgroundFromImage();
  },
  async beforeRouteEnter(to, from, next) {
    await fetchData(to.params.reciter, to.params.album, to.params.track);
    next();
  },
  async beforeRouteUpdate(to, from, next) {
    await fetchData(to.params.reciter, to.params.album, to.params.track);
    next();
  },
};
</script>

<style lang="scss" scoped>
.track-hero {
  width: 100%;
  padding-bottom: 80px;

  .track-hero__content {
    max-width: 1000px;
    padding: 24px;
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
  margin-top: -104px;
  padding: 0 8px;
  max-width: 1024px;
  margin-left: auto;
  margin-right: auto;

  .track-page-content__card {
    padding: 24px;
    margin-bottom: 24px;
    height: 200px;

    &--lyrics {
      height: 500px;

      .lyrics__empty {
        display: flex;
        justify-content: center;
        color: rgba(0,0,0,0.3);
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
    background: linear-gradient(to bottom right, #E90500, #FA6000);
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

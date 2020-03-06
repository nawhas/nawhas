<template>
  <div @click="goToTrack()">
    <v-card class="track-card" :style="{ 'background-color': background }">
      <div class="track-card__text" :style="{ 'color': textColor }">
        <div class="track-card__name body-2" :title="title">
          {{ title }}
        </div>
        <div class="track-card__album caption" :title="album.title">
          {{ album.title }}
        </div>
        <div class="track-card__reciter-year caption" :title="reciterYear">
          {{ reciterYear }}
        </div>
      </div>
      <div class="track-card__album-art">
        <div class="track-card__album-art-gradient"
             v-if="colored && album.artwork"
             :style="{background: gradient}">
        </div>
        <img crossorigin ref="artwork" :src="artwork" :alt="title"/>
      </div>
    </v-card>
  </div>
</template>

<script>
import * as Vibrant from 'node-vibrant';

export default {
  name: 'TrackCard',
  props: ['title', 'slug', 'album', 'reciter', 'showReciter', 'colored'],
  mounted() {
    if (this.colored && this.album.artwork) {
      this.setBackgroundFromImage();
    }
  },
  methods: {
    setBackgroundFromImage() {
      Vibrant.from(this.artwork)
        .getPalette().then((palette) => {
          const swatch = palette.DarkMuted;
          if (!swatch) {
            return;
          }
          this.background = swatch.getHex();
          this.textColor = swatch.getBodyTextColor();
        });
    },
    goToTrack() {
      this.$router.push(`/reciters/${this.reciter.slug}/albums/${this.album.year}/tracks/${this.slug}`);
    },
  },
  data() {
    return {
      background: '#ffffff',
      textColor: '#333',
    };
  },
  computed: {
    artwork() {
      return this.album.artwork || '/img/default-album-image.png';
    },
    reciterYear() {
      if (this.showReciter) {
        return `${this.reciter.name} • ${this.album.year}`;
      }
      return this.album.year;
    },
    gradient() {
      const rgb = Vibrant.Util.hexToRgb(this.background);
      return `linear-gradient(
        to right,
        rgba(${rgb.join(', ')}, 1),
        rgba(${rgb.join(', ')}, 0.7),
        rgba(${rgb.join(', ')}, 0)
      `;
    },
  },
};
</script>

<style lang="scss" scoped>
@import "../styles/theme";
.track-card {
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: gray;
  cursor: pointer;
  @include transition(box-shadow, background-color);

  &:hover {
    @include elevation(4);
  }

  .track-card__text {
    padding: 16px 16px 16px 24px;
    overflow: hidden;
    @include transition(color);

    .track-card__name, .track-card__album, .track-card__reciter-year {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: auto;
    }

    .track-card__name {
      font-size: 0.95rem !important;
      line-height: initial !important;
      margin-bottom: 2px;
    }

    .track-card__reciter-year {
      line-height: initial;
    }
  }

  .track-card__album-art {
    width: 96px;
    height: 96px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    .track-card__album-art-gradient {
      width: 28%;
      height: 100%;
      position: absolute;
      top: 0;
      left: -2px;
      @include transition(background);
    }

    > img {
      width: 100%;
      height: auto;
    }
  }
}
</style>

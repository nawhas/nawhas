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
        <div
          v-if="colored && album.artwork"
          class="track-card__album-art-gradient"
          :style="{background: gradient}"
        />
        <img ref="artwork" crossorigin :src="artwork" :alt="title">
      </div>
    </v-card>
  </div>
</template>

<script>
import * as Vibrant from 'node-vibrant';

export default {
  name: 'TrackCard',
  props: {
    title: {
      type: String,
      required: true,
    },
    slug: {
      type: String,
      required: true,
    },
    album: {
      type: Object,
      required: true,
    },
    reciter: {
      type: Object,
      required: true,
    },
    colored: {
      type: Boolean,
      default: false,
    },
    showReciter: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      vibrantBackgroundColor: '#ffffff',
      vibrantTextColor: null,
    };
  },
  computed: {
    background() {
      if (this.vibrantBackgroundColor !== '#ffffff') {
        return this.vibrantBackgroundColor;
      }
      if (this.isDark) {
        return null;
      }
      return '#ffffff';
    },
    textColor() {
      if (this.vibrantTextColor !== null) {
        return this.vibrantTextColor;
      }
      if (this.isDark) {
        return null;
      }
      return '#333';
    },
    isDark() {
      return this.$vuetify.theme.dark;
    },
    artwork() {
      return this.album.artwork || '/defaults/default-album-image.png';
    },
    reciterYear() {
      if (this.showReciter) {
        return `${this.reciter.name} • ${this.album.year}`;
      }
      return this.album.year;
    },
    gradient() {
      const rgb = Vibrant.Util.hexToRgb(this.vibrantBackgroundColor);
      return `linear-gradient(
        to right,
        rgba(${rgb.join(', ')}, 1),
        rgba(${rgb.join(', ')}, 0.7),
        rgba(${rgb.join(', ')}, 0)
      `;
    },
  },
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
          this.vibrantBackgroundColor = swatch.getHex();
          this.vibrantTextColor = swatch.getBodyTextColor();
        });
    },
    goToTrack() {
      this.$router.push(`/reciters/${this.reciter.slug}/albums/${this.album.year}/tracks/${this.slug}`);
    },
  },
};
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.track-card {
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
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
    flex: none;

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

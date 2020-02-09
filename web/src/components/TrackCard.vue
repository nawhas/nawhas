<template>
  <div @click="goToTrack()">
    <v-card class="track-card" :style="{ 'background-color': background }">
      <div class="track-card__text" :style="{ 'color': textColor }">
        <div class="track-card__name body-2" :title="name">
          {{ name }}
        </div>
        <div class="track-card__album caption" :title="album.name">
          {{ album.name }}
        </div>
        <div class="track-card__reciter-year caption" :title="reciterYear">
          {{ reciterYear }}
        </div>
      </div>
      <div class="track-card__album-art">
        <div class="track-card__album-art-gradient" :style="{background: gradient}"></div>
        <img crossorigin ref="artwork" :src="album.artwork" :alt="name"/>
      </div>
    </v-card>
  </div>
</template>

<script>
import Vibrant from 'node-vibrant';

export default {
  name: 'track-card',
  props: ['name', 'slug', 'album', 'reciter', 'showReciter'],
  mounted() {
    this.setBackgroundFromImage();
  },
  methods: {
    setBackgroundFromImage() {
      Vibrant.from(this.$refs.artwork.src).getPalette().then((palette) => {
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
      background: '#444444',
      textColor: 'white',
    };
  },
  computed: {
    reciterYear() {
      if (this.showReciter) {
        return `${this.reciter.name} • ${this.album.year}`;
      }
      return this.album.year;
    },
    gradient() {
      const rgb = Vibrant.Util.hexToRgb(this.background);
      return `linear-gradient(to right, rgba(${rgb.join(', ')}, 1), rgba(${rgb.join(', ')}, 0)`;
    },
  },
};
</script>

<style lang="scss" scoped>
.track-card {
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: gray;
  cursor: pointer;
  // will-change: box-shadow background-color;
  // transition: background-color $transition, box-shadow $transition;

  &:hover {
    // elevation(8);
  }

  .track-card__text {
    padding: 16px 16px 16px 24px;
    will-change: color;
    // transition: color $transition;
    overflow: hidden;

    .track-card__name, .track-card__album, .track-card__reciter-year {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: auto;
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
      width: 30%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      will-change: background;
      // transition: background $transition,
    }

    > img {
      width: 100%;
      height: auto;
    }
  }
}
</style>

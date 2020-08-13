<template>
  <v-card class="track-card" :style="{ 'background-color': background }" :to="uri">
    <div class="track-card__text" :style="{ 'color': textColor }">
      <div class="track-card__name body-2" :title="track.title">
        {{ track.title }}
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
      <img ref="artwork" crossorigin :src="artwork" :alt="track.title">
    </div>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';
import Vibrant from 'node-vibrant';
import { getTrackUri, Track } from '@/entities/track';
import { Album, getAlbumArtwork } from '@/entities/album';

@Component
export default class TrackCard extends Vue {
  @Prop({
    type: Object,
    required: true,
    validator: (track: Track) => track.album !== undefined && track.reciter !== undefined,
  })
  private readonly track!: Track;

  @Prop({ type: Boolean, default: false })
  private readonly colored!: boolean;

  @Prop({ type: Boolean, default: false })
  private readonly showReciter!: boolean;

  private vibrantBackgroundColor: string = '#ffffff';
  private vibrantTextColor: string|null = null;

  mounted() {
    if (this.colored && this.album.artwork) {
      this.setBackgroundFromImage();
    }
  }

  get album(): Album {
    if (this.track.album === undefined) {
      throw new Error('Track has no album object');
    }

    return this.track.album;
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }

  get textColor() {
    if (this.vibrantTextColor !== null) {
      return this.vibrantTextColor;
    }
    if (this.isDark) {
      return null;
    }
    return '#333';
  }

  get background(): string|null {
    if (this.vibrantBackgroundColor !== '#ffffff') {
      return this.vibrantBackgroundColor;
    }
    if (this.isDark) {
      return null;
    }
    return '#ffffff';
  }

  get artwork() {
    return getAlbumArtwork(this.album);
  }

  get reciterYear() {
    if (this.showReciter && this.track.reciter) {
      return `${this.track.reciter.name} • ${this.album.year}`;
    }
    return this.track.year;
  }

  get gradient() {
    const rgb = Vibrant.Util.hexToRgb(this.vibrantBackgroundColor);
    return `linear-gradient(
        to right,
        rgba(${rgb?.join(', ')}, 1),
        rgba(${rgb?.join(', ')}, 0.7),
        rgba(${rgb?.join(', ')}, 0)
      `;
  }

  get uri() {
    return getTrackUri(this.track);
  }

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
  }
}
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

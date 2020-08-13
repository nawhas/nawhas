<template>
  <v-card :to="link" :class="classObject" :style="{ 'background-color': background }">
    <div class="reciter-card__avatar">
      <v-avatar size="40" class="avatar">
        <img ref="avatarElement" crossorigin :src="image" :alt="name">
      </v-avatar>
    </div>
    <div class="reciter-card__text" :style="{ 'color': textColor }">
      <div class="reciter-card__name body-2" :title="name">
        {{ name }}
      </div>
      <div v-if="related" class="reciter-card__name caption">
        {{ related.albums | pluralize('album', 'albums') }}
      </div>
    </div>
  </v-card>
</template>

<script lang="ts">
/* eslint-disable dot-notation */
import { Component, Vue, Prop } from 'nuxt-property-decorator';
import Vibrant from 'node-vibrant';

@Component
export default class ReciterCard extends Vue {
  @Prop({ type: String }) private id!: any;
  @Prop({ type: String }) private name!: any;
  @Prop({ type: String }) private slug!: any;
  @Prop({ type: String }) private avatar!: any;
  @Prop({ type: Object }) private related!: any;
  @Prop({ type: String }) private createdAt!: any;
  @Prop({ type: String }) private updatedAt!: any;
  @Prop() private featured!: any;

  private vibrantBackgroundColor: null|string = null;
  private vibrantTextColor: null|string = null;

  get image() {
    return this.avatar || '/defaults/default-reciter-avatar.png';
  }

  get link(): string {
    return `reciters/${this.slug}`;
  }

  get classObject() {
    return {
      'reciter-card': true,
      'reciter-card--featured': this.featured !== undefined,
    };
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }

  get background() {
    if (this.vibrantBackgroundColor !== null) {
      return this.vibrantBackgroundColor;
    }
    if (this.isDark) {
      return null;
    }
    if (this.featured !== undefined) {
      return '#444444';
    }
    return 'white';
  }

  get textColor() {
    if (this.vibrantTextColor !== null) {
      return this.vibrantTextColor;
    }
    if (this.isDark) {
      return null;
    }
    if (this.featured !== undefined) {
      return 'white';
    }
    return '#333';
  }

  mounted() {
    if (this.featured !== undefined) {
      this.setBackgroundFromImage();
    }
  }

  setBackgroundFromImage() {
    Vibrant.from(this.image)
      .getPalette()
      .then((palette) => {
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

.reciter-card {
  padding: 16px;
  display: flex;
  align-items: center;
  cursor: pointer;
  @include transition(background-color, box-shadow);
  @include elevation(2);

  &:hover:not(.reciter-card--featured) {
    background-color: rgba(0, 0, 0, 0.1) !important;
  }

  &--featured {
    background: gray;
    @include elevation(4);

    &:hover {
      @include elevation(8);
    }

    .reciter-card__text .reciter-card__name {
      font-weight: bold;
    }
  }

  .reciter-card__text {
    margin-left: 16px;
    overflow: hidden;
    @include transition(color);

    .reciter-card__name {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: auto;
    }
  }

  .reciter-card__avatar .avatar {
    border: 2px solid white;
    overflow: hidden;
  }
}
</style>

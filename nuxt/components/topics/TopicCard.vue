<template>
  <v-card :to="link" :class="classObject" :style="{ 'background-color': background }">
    <div class="topic-card__avatar">
      <lazy-image ref="avatarElement" class="avatar" crossorigin :src="image" :alt="topic.name" />
    </div>
    <div class="topic-card__text" :style="{ 'color': textColor }">
      <div class="topic-card__name body-2" :title="topic.name">
        {{ topic.name }}
      </div>
      <div v-if="topic.related" class="topic-card__name caption">
        {{ topic.related.tracks | pluralize('track', 'tracks') }}
      </div>
    </div>
  </v-card>
</template>

<script lang="ts">

import { Component, Vue, Prop } from 'nuxt-property-decorator';
import Vibrant from 'node-vibrant';
import LazyImage from '@/components/utils/LazyImage.vue';
import { getTopicImage, getTopicUri, Topic } from '@/entities/topic';

@Component({
  components: {
    LazyImage,
  },
})
export default class TopicCard extends Vue {
  @Prop({ type: Object, required: true }) private topic!: Topic;
  @Prop() private featured!: boolean;

  private vibrantBackgroundColor: null|string = null;
  private vibrantTextColor: null|string = null;

  get image() {
    return getTopicImage(this.topic);
  }

  get link(): string {
    return getTopicUri(this.topic);
  }

  get classObject() {
    return {
      'topic-card': true,
      'topic-card--featured': this.featured,
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
    if (this.featured) {
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
    if (this.featured) {
      return 'white';
    }
    return '#333';
  }

  mounted() {
    if (this.featured) {
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

.topic-card {
  padding: 16px;
  display: flex;
  align-items: center;
  cursor: pointer;
  @include transition(background-color, box-shadow);
  @include elevation(2);

  &:hover:not(.topic-card--featured) {
    background-color: rgba(0, 0, 0, 0.1) !important;
  }

  &--featured {
    background: gray;
    @include elevation(4);

    &:hover {
      @include elevation(8);
    }

    .topic-card__text .topic-card__name {
      font-weight: bold;
    }
  }

  .topic-card__text {
    margin-left: 16px;
    overflow: hidden;
    @include transition(color);

    .topic-card__name {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: auto;
    }
  }

  .topic-card__avatar .avatar {
    width: 46px;
  }
}
</style>

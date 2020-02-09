<template>
  <div @click="goToReciter()">
    <v-card :class="classObject" :style="{ 'background-color': background }">
      <div class="reciter-card__avatar">
        <v-avatar size="48px">
          <img crossorigin ref="avatarElement" :src="avatar" :alt="name">
        </v-avatar>
      </div>
      <div class="reciter-card__text" :style="{ 'color': textColor }">
        <div class="reciter-card__name body-2" :title="name">
          {{ name }}
        </div>
        <div class="reciter-card__albums caption" :title="albumCount + ' Albums'">
          {{ albumCount }} Albums
        </div>
      </div>
    </v-card>
  </div>
</template>

<script>
import Vibrant from 'node-vibrant';

export default {
  name: 'reciter-card',
  props: [
    'id', 'name', 'slug', 'albumCount', 'avatar', 'createdAt', 'updatedAt', 'featured',
  ],
  mounted() {
    if (this.featured !== undefined) {
      const image = this.$refs.avatarElement;
      if (image && image.src) {
        this.setBackgroundFromImage(image);
      }
    }
  },
  methods: {
    goToReciter() {
      this.$router.push(`/reciters/${this.slug}`);
    },
    setBackgroundFromImage(image) {
      Vibrant.from(image.src).getPalette().then((palette) => {
        const swatch = palette.DarkVibrant;
        if (!swatch) {
          return;
        }
        this.background = swatch.getHex();
        this.textColor = swatch.getBodyTextColor();
      });
    },
  },
  data() {
    if (this.featured !== undefined) {
      return {
        background: '#444444',
        textColor: 'white',
      };
    }

    return {
      background: 'transparent',
      textColor: 'black',
    };
  },
  computed: {
    classObject() {
      return {
        'reciter-card': true,
        'reciter-card--featured': this.featured !== undefined,
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.reciter-card {
  padding: 16px;
  display: flex;
  /*align-items center;*/
  cursor: pointer;
  /*will-change: box-shadow background-color;*/
  // transition: background-color $transition, box-shadow $transition;
  // elevation(0);

  &:hover:not(.reciter-card--featured) {
    background-color: rgba(0, 0, 0, 0.1) !important;
  }

  &--featured {
    background: gray;
    // elevation(4);

    &:hover {
      // elevation(8);
    }
  }

  .reciter-card__text {
    margin-left: 16px;
    will-change: color;
    // transition: color $transition;
    overflow: hidden;

    .reciter-card__name {
//      white-space nowrap;
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

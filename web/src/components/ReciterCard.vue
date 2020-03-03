<template>
  <div @click="goToReciter()">
    <v-card :class="classObject" :style="{ 'background-color': background }">
      <div class="reciter-card__avatar">
        <v-avatar size="40">
          <img crossorigin ref="avatarElement" :src="image" :alt="name" />
        </v-avatar>
      </div>
      <div class="reciter-card__text" :style="{ 'color': textColor }">
        <div class="reciter-card__name body-2" :title="name">{{ name }}</div>
         <div class="reciter-card__name caption" v-if="related">
           {{ related.albums | pluralize('album', 'albums') }}
         </div>
      </div>
    </v-card>
  </div>
</template>

<script>
import Vibrant from 'node-vibrant';

export default {
  name: 'ReciterCard',
  props: ['id', 'name', 'slug', 'avatar', 'related', 'createdAt', 'updatedAt', 'featured'],
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
      this.$router.push({
        name: 'reciters.show',
        params: { reciter: this.slug },
      });
    },
    setBackgroundFromImage(image) {
      Vibrant.from(image.src)
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
  },
  data() {
    if (this.featured !== undefined) {
      return {
        background: '#444444',
        textColor: 'white',
      };
    }

    return {
      background: 'white',
      textColor: '#333',
    };
  },
  computed: {
    image() {
      return this.avatar || '/img/default-reciter-avatar.png';
    },
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
@import '../styles/theme';

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

<template>
  <v-app>
    <v-content>
      <div class="print__header">
        <div class="print__header__title">
          <div class="print__header__title--track--name">{{ track.title }}</div>
          <div class="print__header__title--track--meta">{{ track.reciter.name }} - {{ track.year }}</div>
        </div>
        <v-spacer></v-spacer>
        <div class="print__header_logo">
          <img src="./../assets/logo.png" />
        </div>
      </div>
      <div class="print__content" v-html="prepareLyrics(track.lyrics.content)"></div>
    </v-content>
  </v-app>
</template>

<script>
import { getTrack } from '@/services/tracks';

export default {
  mounted() {
    if (this.trackData) {
      this.track = this.trackData;
      window.print();
      window.addEventListener('focus', this.goBack);
    }
    if (!this.track) {
      const { reciter, album, track } = this.$route.params;
      getTrack(reciter, album, track, { include: 'reciter,lyrics' })
        .then((response) => {
          this.track = response.data;
        })
        .then(() => {
          window.print();
          window.addEventListener('focus', this.goBack);
        });
    }
  },
  beforeDestroy() {
    window.removeEventListener('focus', this.goBack);
  },
  props: ['trackData'],
  data() {
    return {
      track: null,
    };
  },
  methods: {
    prepareLyrics(content) {
      return content.replace(/\n/gi, '<br>');
    },
    goBack() {
      this.$router.go(-1);
    },
  },
};
</script>

<style lang="scss" scoped>
.print__header {
  width: 100%;
  display: flex;
  border-bottom: rgba(0, 0, 0, 0.3) solid 1px;
  font-family: 'Roboto Slab', sans-serif;

  &__title {
    &--track--name {
      font-size: 20px;
    }

    &--track--meta {
      font-size: 14px;
    }
  }
}

.print__content {
  padding-top: 6mm;
  -webkit-column-count: 2; /* Chrome, Safari, Opera */
  -moz-column-count: 2; /* Firefox */
  column-count: 2;
  font-family: 'Roboto Slab', sans-serif;
}
</style>

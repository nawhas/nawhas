<template>
  <v-app>
    <v-content v-if="track" class="content">
      <div class="print__header">
        <div class="print__header__title">
          <div class="print__header__title--track--name">{{ track.title }}</div>
          <div class="print__header__title--track--meta">{{ track.reciter.name }} - {{ track.year }}</div>
        </div>
        <v-spacer />
        <div class="print__header_logo">
          <img src="../../web/src/assets/logo.svg" alt="Nawhas.com" />
        </div>
      </div>
      <lyrics-renderer v-if="track.lyrics" class="print__content" :track="track" />
      <div
        v-else
        class="print__content print__content--empty"
      >We don't have a write-up of this nawha yet.</div>
    </v-content>
    <v-content v-else class="content">Loading...</v-content>
  </v-app>
</template>

<script>
/* eslint-disable dot-notation */
import Vue from 'vue';
import LyricsRenderer from '@/components/lyrics/LyricsRenderer.vue';
import { TrackIncludes } from '@/api/tracks';
import { getTrackUri } from '@/entities/track';

export default Vue.extend({
  name: 'LyricsPrintLayout',

  components: {
    LyricsRenderer,
  },

  async fetch() {
    const { reciter, album, track } = await this.$route.params;
    this.fetchedTrack = this.$api.tracks.get(reciter, album, track, {
      include: [
        TrackIncludes.Reciter,
        TrackIncludes.Lyrics,
        'album.tracks',
      ],
    });
  },

  data: () => ({
    fetchedTrack: undefined,
    timeout: undefined,
  }),

  computed: {
    track() {
      return this.fetchedTrack;
    },
  },

  mounted() {
    const handler = () => {
      this.goBackToTrack();
    };
    this.$el['__onPrintCompleteHandler__'] = handler;
    window.addEventListener('afterprint', handler);

    if (!this.track) {
      return;
    }

    this.triggerPrint();
  },

  updated() {
    if (!this.track) {
      return;
    }

    this.triggerPrint();
  },

  beforeDestroy() {
    window.removeEventListener('afterprint', this.$el['__onPrintCompleteHandler__']);
    delete this.$el['__onPrintCompleteHandler__'];
  },

  methods: {
    triggerPrint() {
      window.clearTimeout(this.timeout);
      window.timeout = window.setTimeout(() => {
        window.print();
      }, 500);
    },
    goBackToTrack() {
      if (!this.track) {
        return;
      }

      const url = getTrackUri(this.track, this.track.reciter);

      this.$router.replace(url);
    },
  },
});
</script>

<style lang="scss">
.print__header {
  width: 100%;
  display: flex;
  border-bottom: rgba(0, 0, 0, 0.3) solid 1px;
  font-family: "Roboto Slab", sans-serif;

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
  column-count: 2;
  font-weight: 400;
  font-size: 1rem;
  line-height: 2.1rem;

  .lyrics__spacer {
    width: 1px;
    height: 12px;
  }

  .lyrics__group__lines__line {
    display: flex;
    align-items: center;

    .lyrics__repeat {
      margin-left: 8px;
      padding: 4px 6px;
      text-align: center;
      border-radius: 8px;
      font-size: 12px;
      font-family: "Roboto Mono", monospace;
      font-weight: 600;
      line-height: 12px;
      border: 1px solid rgba(0, 0, 0, 0.6);
    }
  }
}

.print__content--empty {
  color: rgba(0, 0, 0, 0.6);
  font-size: 20px;
  column-count: 1;
  padding: 20px;
  text-align: center;
}

.content {
  background: white !important;
  color: black !important;
  padding: 24px !important;
}

@media print {
  .content {
    padding: 0 !important;
  }
}
</style>

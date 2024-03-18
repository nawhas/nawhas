<router>
  path: /print/:reciterId/:albumId/:trackId
  name: "print.lyrics"
</router>

<template>
  <div>
    <div class="print__header">
      <div class="print__header__title">
        <div class="print__header__title--track--name">
          {{ track.title }}
        </div>
        <div class="print__header__title--track--meta">
          {{ track.reciter.name }} - {{ track.year }}
        </div>
      </div>
      <v-spacer />
      <div class="masthead__logo">
        <logo-icon class="masthead__logo__icon" />
        <logo-wordmark class="masthead__logo__wordmark" />
      </div>
    </div>
    <lyrics-renderer v-if="track.lyrics" class="print__content" :track="track" />
    <div
      v-else
      class="print__content print__content--empty"
    >
      We don't have a write-up of this nawha yet.
    </div>
  </div>
</template>

<script lang="ts">

import Vue from 'vue';
import { MetaInfo } from 'vue-meta';
import LyricsRenderer from '@/components/lyrics/LyricsRenderer.vue';
import { TrackIncludes } from '@/api/tracks';
import { Track } from '@/entities/track';
const LogoIcon = require('@/assets/svg/icon.svg?inline');
const LogoWordmark = require('@/assets/svg/wordmark.svg?inline');

interface Data {
  track: Track | null;
}

export default Vue.extend({
  name: 'LyricsPrintLayout',

  components: {
    LyricsRenderer,
    LogoIcon,
    LogoWordmark,
  },

  layout: 'print',

  data(): Data {
    return {
      track: null,
    };
  },

  async fetch() {
    const { reciterId, albumId, trackId } = this.$route.params;
    this.track = await this.$api.tracks.get(reciterId, albumId, trackId, {
      include: [
        TrackIncludes.Reciter,
        TrackIncludes.Lyrics,
      ],
    });
  },

  head(): MetaInfo {
    let title = 'Loading...';

    if (this.track) {
      title = `${this.track.title} (${this.track.year}) - Nawha by ${this.track.reciter?.name}`;
    }

    return { title };
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

.masthead__logo {
  height: 38px;
  cursor: pointer;
  display: flex;
  align-items: center;

  &__icon {
    height: 38px;
  }
  &__wordmark {
    height: 16px;
    margin-left: 8px;
  }
}
</style>

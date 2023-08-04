<template>
  <nuxt-link :to="track.meta.url" class="link">
    <div :class="{ 'track-result': true, 'track-result--dark': isDark }">
      <div class="track-result__artwork">
        <v-avatar tile size="40">
          <img crossorigin :src="image" :alt="track.title">
        </v-avatar>
      </div>
      <div class="track-result__text" :class="{ 'track-result__text--dark': isDark }">
        <div class="track-result__name body-1" :title="track.title">
          <div class="track-result__title" v-html="highlighted.title" />
          <div class="track-result__year body-2" v-html="highlighted.year" />
        </div>
        <div class="track-result__reciter body-2" v-html="highlighted.reciter" />
        <div class="track-result__lyrics body-2">
          <span v-if="highlighted.lyrics" v-html="highlighted.lyrics" />
          <span v-else>No lyrics available</span>
        </div>
      </div>
    </div>
  </nuxt-link>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';

interface TrackSearchResult {
  // Track UUID
  id: string;
  // Track Title
  title: string;
  // Reciter Name
  reciter: string;
  // Album Title
  album: string;
  // Album Year
  year: string;
  // Rendered Lyrics
  lyrics: string|null;
  // Metadata
  meta: {
    slug: string;
    artwork: string|null;
    url: string;
  };
  _formatted?: Formatted;
}

type Formatted = Omit<TrackSearchResult, '_formatted'>;

@Component
export default class TrackResult extends Vue {
  @Prop({ type: Object, required: true }) private readonly track!: TrackSearchResult;

  get highlighted(): Formatted {
    return this.track._formatted ?? this.track;
  }

  get image() {
    return this.track.meta.artwork ?? require('@/assets/img/defaults/default-album-image.png');
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }
}
</script>

<style lang="scss">
.link {
  text-decoration: none;
}
.track-result {
  color: rgba(0, 0, 0, 0.76);
  padding: 8px 16px;
  display: flex;
  align-items: center;
  cursor: pointer;
  will-change: background-color;
  background-color: rgba(0,0,0,0.0);
  transition: background-color 280ms;

  &:hover {
    background-color: rgba(0,0,0,0.08);
  }

  .track-result__text {
    margin-left: 16px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    width: 100%;

    em, mark {
      font-style: inherit;
      background: none;
      padding-bottom: 1px;
      border-bottom: 2px solid orangered;
    }
  }

  .track-result__name {
    justify-content: space-between;
    align-items: baseline;
    display: flex;
    width: 100%;
  }
  .track-result__year {
    display: block;
  }
  .track-result__reciter {
    margin-bottom: 2px;
  }
  .track-result__lyrics {
    font-style: italic;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
}

.track-result--dark {
  color: white;
  .track-result__text {
    em, mark {
      color: wheat;
    }
  }
}
</style>

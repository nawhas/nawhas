<template>
  <div>
    <v-list v-if="tracks" class="pa-0">
      <v-list-item
        v-for="(track, index) in tracks"
        :key="index"
        :to="getTrackLink(track)"
        :two-line="metadata"
      >
        <v-list-item-avatar>
          <v-avatar size="36">
            <span>{{ index+1 }}</span>
          </v-avatar>
        </v-list-item-avatar>
        <v-list-item-content>
          <v-list-item-title>
            {{ track.title }}
          </v-list-item-title>
          <v-list-item-subtitle v-if="metadata">
            {{ track.reciter.name }} &bull; {{ track.album.year }}
          </v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action class="track__features">
          <v-icon
            :class="{
              'material-icons-outlined': true,
              track__feature: true,
              'track__feature--disabled': !hasLyrics(track) && !isDark,
              'track__feature--disabled--dark': !hasLyrics(track) && isDark
            }"
          >
            <template v-if="hasLyrics(track)">
              speaker_notes
            </template>
            <template v-else>
              speaker_notes_off
            </template>
          </v-icon>
          <v-btn
            icon
            :disabled="!hasAudioFile(track)"
            :class="{
              track__feature: true,
              'track__feature--disabled': !hasAudioFile(track) && !isDark,
              'track__feature--disabled--dark': !hasAudioFile(track) && isDark
            }"
            @click.prevent="playTrack(track)"
          >
            <v-icon>
              <template v-if="hasAudioFile(track)">
                play_circle_outline
              </template>
              <template v-else>
                volume_off
              </template>
            </v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list>
    <v-list v-else>
      <v-skeleton-loader
        v-for="index in count"
        :key="index"
        type="list-item-avatar"
        class="py-1"
        boilerplate
      />
    </v-list>
  </div>
</template>

<script lang="ts">
import {
  Component, Prop, Vue,
} from 'nuxt-property-decorator';
import { hasAudioFile, hasLyrics } from '@/utils/tracks';

@Component
export default class TrackList extends Vue {
  @Prop({ type: Array }) private readonly tracks;
  @Prop({ type: Boolean, default: false }) private readonly metadata!: boolean;
  @Prop({ type: Number, default: 6 }) private readonly count!: number;

  private hasAudioFile = hasAudioFile;
  private hasLyrics = hasLyrics;

  get playable() {
    if (!this.tracks) {
      return [];
    }

    return this.tracks.filter((track) => hasAudioFile(track));
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }

  playTrack(track) {
    this.$store.commit('player/PLAY_ALBUM', { tracks: this.playable, start: track });
  }

  getTrackLink(track) {
    return `/reciters/${track.reciter.slug}/albums/${track.album.year}/tracks/${track.slug}`;
  }
}
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.track__features {
  white-space: nowrap;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  flex-direction: row;

  .track__feature {
    margin-left: 6px;
    color: map-deep-get($colors, 'deep-orange', 'darken-3');
  }

  .track__feature--disabled {
    color: rgba(0, 0, 0, 0.1);
  }
  .track__feature--disabled--dark {
    color: rgba(map-get($shades, 'white'), 0.5);
  }
}
</style>

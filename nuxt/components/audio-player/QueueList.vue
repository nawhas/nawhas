<template>
  <v-list color="transparent" :dark="dark">
    <v-list-item
      v-for="{ id, track } in queue"
      :key="id"
      :class="{
        'queue-item': true,
        'queue-item--active': isCurrentTrack(id),
        'queue-item--dark': isDark,
      }"
      @click="skipToTrack(id)"
    >
      <v-list-item-avatar tile class="queue-item__album-art">
        <img crossorigin :src="getTrackArtwork(track)" :alt="track.title">
      </v-list-item-avatar>

      <v-list-item-content>
        <v-list-item-title>{{ track.title }}</v-list-item-title>
        <v-list-item-subtitle>{{ track.reciter.name }} - {{ track.year }}</v-list-item-subtitle>
      </v-list-item-content>

      <v-list-item-action>
        <v-btn v-if="!isCurrentTrack(id)" icon @click="removeTrackFromQueue(id)">
          <v-icon>remove_circle_outline</v-icon>
        </v-btn>
        <v-progress-circular
          v-else
          class="playback-progress"
          :size="20"
          :rotate="-90"
          :value="progress"
          :color="isDark ? 'accent' : 'primary'"
        />
      </v-list-item-action>
    </v-list-item>
  </v-list>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';
import { TrackQueue, QueuedTrack } from '@/store/player';
import { getAlbumArtwork } from '@/entities/album';

@Component
export default class QueueList extends Vue {
  @Prop({ type: Boolean, default: false }) private readonly dark!: boolean;
  /**
   * Gets the current queue from the player store
   */
  get queue(): TrackQueue {
    return this.$store.getters['player/queue'];
  }

  /**
   * Gets the current QueuedTrack object from the player store
   */
  get currentQueuedTrack(): QueuedTrack|null {
    return this.$store.getters['player/track'];
  }

  /**
   * Get the progress from the store
   */
  get progress(): number {
    return this.$store.getters['player/progress'];
  }

  get isDark(): boolean {
    return this.dark || this.$vuetify.theme.dark;
  }

  getTrackArtwork(track): string {
    return getAlbumArtwork(track.album);
  }

  /**
   * Check to see weather a track is the current track
   */
  isCurrentTrack(id: string) {
    return (this.currentQueuedTrack && this.currentQueuedTrack.id === id);
  }

  /**
   * Removes the track from the queue
   */
  removeTrackFromQueue(id: string) {
    this.$store.commit('player/REMOVE_TRACK', { id });
    // resetting queueMenu back to true to re-render the height of the queue menu
    this.$emit('change');
  }

  /**
   * Skip to the selected track in the queue
   */
  skipToTrack(id) {
    this.$store.commit('player/SKIP_TO_TRACK', { id });
  }
}
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.queue-item--active {
  background-color: rgba(0,0,0,0.1);
}
.queue-item--dark.queue-item--active {
  background-color: rgba(255,255,255,0.1);
}
.playback-progress {
  margin-right: 8px;
}
.queue-item__album-art {
  border: 2px solid white;
}
</style>

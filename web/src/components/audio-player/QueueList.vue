<template>
  <v-list>
    <v-list-item
      v-for="{ id, track } in queue"
      :key="id"
      @click="skipToTrack(id)"
      :class="{
        'queue-item': true,
        'queue-item--active': isCurrentTrack(id),
        'queue-item--active--dark': isDark && isCurrentTrack(id),
      }"
    >
      <v-list-item-avatar tile class="queue-item__album-art">
        <img crossorigin :src="getTrackArtwork(track)" :alt="track.title">
      </v-list-item-avatar>

      <v-list-item-content>
        <v-list-item-title>{{ track.title }}</v-list-item-title>
        <v-list-item-subtitle>{{ track.reciter.name }} - {{ track.year }}</v-list-item-subtitle>
      </v-list-item-content>

      <v-list-item-action>
        <v-btn icon v-if="!isCurrentTrack(id)" @click="removeTrackFromQueue(id)">
          <v-icon>remove_circle_outline</v-icon>
        </v-btn>
        <v-progress-circular
          class="playback-progress"
          v-else
          :size="20"
          :rotate="-90"
          :value="progress"
          color="primary" />
      </v-list-item-action>
    </v-list-item>
  </v-list>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import { TrackQueue, QueuedTrack } from '@/store/modules/player';

@Component
export default class QueueList extends Vue {
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
    return this.$vuetify.theme.dark;
  }

  getTrackArtwork(track): string {
    if (track.album && track.album.artwork) {
      return track.album.artwork;
    }

    return '/img/default-album-image.png';
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
@import '@/styles/theme';

.queue-item--active {
  background-color: map-deep-get($colors, 'deep-orange', 'lighten-5');
}
.queue-item--active--dark {
  background-color: map-deep-get($colors, 'deep-orange', 'darken-0');
}
.playback-progress {
  margin-right: 8px;
}
.queue-item__album-art {
  border: 2px solid white;
}
</style>

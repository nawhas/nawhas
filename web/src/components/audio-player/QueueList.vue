<template>
  <v-list>
    <v-list-item
      v-for="{ id, track } in queue"
      :key="id"
      @click="skipToTrack(id)"
      :class="{'queue-item': true, 'queue-item--active': isCurrentTrack(id)}"
    >
      <v-list-item-avatar>
        <img src="/img/default-album-image.png">
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
    return this.$store.state.player.queue;
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
</style>

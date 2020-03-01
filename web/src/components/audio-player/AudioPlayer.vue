<template>
    <div :class="{ 'audio-player': true, 'audio-player--hovering': hovering, 'audio-player--floating': floating }"
         @mouseenter="hovering = true"
         @mouseleave="hovering = false"
         v-if="track"
    >
      <v-hover class="artwork">
        <template v-slot:default="{ hover }">
          <div>
            <img :src="artwork" />
            <v-fade-transition>
              <v-overlay v-if="hover && floating" absolute>
                <v-btn icon @click="toggleFloating"><v-icon>fullscreen</v-icon></v-btn>
              </v-overlay>
            </v-fade-transition>
          </div>
        </template>
      </v-hover>
      <div class="player-content">
        <div class="seek-bar">
          <v-progress-linear
            :active="duration !== 0"
            v-model="progress"
            color="deep-orange"
            height="8"
            :background-opacity="hovering ? 0.3 : 0"
            class="seek-bar__progress">
          </v-progress-linear>
        </div>
        <v-expand-transition>
          <div class="track-info" v-if="!floating">
            <div class="track-info--track-name body-1" @click="goToTrack">
              {{ track.title }}
            </div>
            <div class="track-info--track-meta body-2">
              {{ track.reciter.name }} &bull; {{ track.year }}
            </div>
          </div>
        </v-expand-transition>
        <div class="player-actions">
          <v-btn icon large @click="previous"><v-icon>skip_previous</v-icon></v-btn>
          <v-btn icon x-large color="deep-orange" @click="togglePlayState">
            <v-icon v-if="playing">pause_circle_filled</v-icon>
            <v-icon v-else>play_circle_filled</v-icon>
          </v-btn>
          <v-btn icon large @click="next" :disabled="!hasNext"><v-icon>skip_next</v-icon></v-btn>
          <v-expand-transition>
            <v-btn icon v-if="floating"><v-icon>more_vert</v-icon></v-btn>
          </v-expand-transition>
        </div>
        <v-expand-transition>
          <div class="player-sub-actions" v-if="!floating">
            <v-menu
              v-model="queueMenu"
              top
              :nudge-left="300"
              offset-y
              :close-on-content-click="false"
            >
              <template v-slot:activator="{ on }">
                <v-btn
                  icon large
                  v-on="on"
                >
                <v-icon>playlist_play</v-icon>
                </v-btn>
              </template>

              <v-card>
                <v-list>
                  <v-list-item
                    v-for="{ id, track } in this.queue"
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
                        color="primary"
                      ></v-progress-circular>
                    </v-list-item-action>
                  </v-list-item>
                </v-list>
              </v-card>
            </v-menu>
            <v-btn @click="toggleFloating" icon large><v-icon>picture_in_picture_alt</v-icon></v-btn>
          </div>
        </v-expand-transition>
      </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator';
import { Howl } from 'howler';
import { PlayerState, TrackQueue, QueuedTrack } from '@/store/modules/player';

interface CachedTrackReference {
  queued: QueuedTrack|null;
  index: number|null;
}

@Component
export default class AudioPlayer extends Vue {
  /* Current audio file time in seconds */
  private seek = 0;
  /* Duration of the audio file in seconds */
  private duration = 0;
  /* Denote whether the user is hovering over the player */
  private hovering = false;
  /* Denote weather the the audio-player is playing */
  private playing = false;
  /* Denote whether the player is "minimized" */
  private floating = false;
  /* Playback engine */
  private howl: Howl|undefined = null;
  /* Cache the current playing track to compare */
  private currentTrack: CachedTrackReference = {
    queued: null,
    index: null,
  };
  /* Denote whether the menu for the queue is 'minimized' */
  private queueMenu = false;

  /**
   * Increase the height of the seek bar when hovering
   * for easier usability.
   */
  get seekBarHeight() {
    return this.hovering ? 10 : 4;
  }

  /**
   * Convenient accessor for the Player store.
   */
  get store(): PlayerState {
    return this.$store.state.player;
  }

  /**
   * Gets the current track from the player store
   */
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  get track(): any {
    const queued: QueuedTrack|null = this.$store.getters['player/track'];
    return queued !== null ? queued.track : null;
  }

  /**
   * Gets the current QueuedTrack object from the player store
   */
  get currentQueuedTrack(): QueuedTrack|null {
    return this.$store.getters['player/track'];
  }

  /**
   * Determine if there's another track in the queue.
   */
  get hasNext(): boolean {
    return this.$store.getters['player/hasNext'];
  }

  /**
   * Determine if there's another track in the queue before this.
   */
  get hasPrevious(): boolean {
    return this.$store.getters['player/hasPrevious'];
  }

  /**
   * Gets the current queue from the player store
   */
  get queue(): TrackQueue {
    return this.store.queue;
  }

  get artwork(): string {
    if (!this.track || !this.track.album.artwork) {
      return '/img/default-album-image.png';
    }

    return this.track.album.artwork;
  }

  get uri(): string|null {
    if (!this.track) {
      return null;
    }

    return this.track.media.data[0].uri;
  }

  /**
   * Update the progress bar with the current playback status.
  */
  get progress(): number {
    if (!this.seek || !this.duration) {
      return 0;
    }
    return (this.seek / this.duration) * 100;
  }

  /**
   * When progress bar is clicked, update Howl to seek to the
   * given position in the audio track.
   */
  set progress(progress: number) {
    if (!this.howl) {
      return;
    }
    this.howl.seek((progress / 100) * this.duration);
  }

  /**
   * Check to see weather a track is the current track
   */
  isCurrentTrack(id: string) {
    return (this.currentTrack.queued && this.currentTrack.queued.id === id);
  }

  /**
   * Removes the track from the queue
   */
  removeTrackFromQueue(id: string) {
    this.$store.commit('player/REMOVE_TRACK', { id });
    // resetting queueMenu back to true to re-render the height of the queue menu
    this.queueMenu = false;
    this.$nextTick(() => this.queueMenu = true);
  }

  goToTrack() {
    this.$router.push({
      name: 'tracks.show',
      params: {
        reciter: this.track.reciter.slug,
        album: this.track.album.year,
        track: this.track.slug,
        trackObject: this.track,
      },
    });
  }

  /**
   * Skip to the selected track in the queue
   */
  skipToTrack(id) {
    this.$store.commit('player/SKIP_TO_TRACK', { id });
  }

  /**
   * If a new track is requested, play the new one.
   */
  @Watch('track')
  onTrackUpdate() {
    // If the current track we're playing is the same as the updated track from
    // the store, don't do anything.
    if (this.currentTrack.queued && this.currentQueuedTrack) {
      if (this.currentTrack.queued.id === this.currentQueuedTrack.id) {
        return;
      }
    }

    this.currentTrack.queued = this.currentQueuedTrack;
    this.currentTrack.index = this.store.current;

    this.stop();
    this.howl = null;
    this.play();
  }


  /**
   * Handle Play/Pause button click.
   */
  togglePlayState() {
    if (this.playing) {
      this.pause();
    } else {
      this.play();
    }
  }

  /**
   * Makes the audio player floating
   */
  toggleFloating() {
    this.floating = !this.floating;
  }

  /**
   * Start playing the current track.
   * If no player is initialized, initialize Howler.
   */
  play() {
    if (!this.howl) {
      this.initializeHowler();
    }

    this.howl.play();
    this.playing = true;
  }

  /**
   * Pause playback if playing.
   */
  pause() {
    if (!this.howl) {
      return;
    }

    this.howl.pause();
    this.playing = false;
  }

  /**
   * Stop playback if playing.
   */
  stop() {
    if (!this.howl) {
      return;
    }

    this.howl.stop();
    this.playing = false;
    this.seek = 0;
    this.duration = 0;
  }

  previous() {
    if (this.hasPrevious && this.progress < 2) {
      this.$store.commit('player/PREVIOUS');
      return;
    }

    // Reset the track if the current track is more than 2 percent complete
    // or if there's no previous track..
    this.howl.seek(0);
  }

  /**
   * Play the next track in the queue
   */
  next() {
    this.$store.commit('player/NEXT');
  }

  /**
   * Every 1/4 of a second, update the progress bar with the
   * current seek time.
   */
  updateSeek() {
    if (!this.howl) {
      return;
    }

    this.seek = this.howl.seek();
    this.duration = this.howl.duration();
  }

  /**
   * Initialize Howler for playback.
   * Bind event listeners to Howl.
   */
  initializeHowler() {
    this.howl = new Howl({
      src: [this.uri],
      html5: true,
    });

    // Register seek binding.
    this.howl.on('play', () => {
      this.playing = true;
      window.setInterval(() => this.updateSeek(), 1000 / 4);
    });

    // Register end binding.
    this.howl.on('end', () => {
      if (this.hasNext) {
        this.next();
      } else {
        this.stop();
      }
    });

    // Register pause binding.
    this.howl.on('pause', () => {
      this.playing = false;
    });

    // if ('mediaSession' in navigator) {
    //   // eslint-disable-next-line no-undef
    //   (navigator as any).mediaSession.metadata = new MediaMetadata({
    //     title: 'Chotey Hazrat',
    //     artist: 'Nadeem Sarwar',
    //     album: '2011',
    //     artwork: [
    //       { src: 'https://localhost:8080/img/default-album-image.png', sizes: '1024x1024', type: 'image/png' },
    //     ],
    //   });
    //   (navigator as any).mediaSession.setActionHandler('play', () => {
    //     this.play();
    //   });
    //   (navigator as any).mediaSession.setActionHandler('pause', () => {
    //     this.pause();
    //   });
    //   // navigator.mediaSession.setActionHandler('seekbackward', function() {});
    //   // navigator.mediaSession.setActionHandler('seekforward', function() {});
    //   // navigator.mediaSession.setActionHandler('previoustrack', function() {});
    //   // navigator.mediaSession.setActionHandler('nexttrack', function() {});
    // }
  }

  /**
   * For development purpose only
   * stops playing and unloads howl on hot reload
   */
  beforeDestroy() {
    if (this.howl) {
      this.howl.unload();
      this.howl = undefined;
    }
  }
}
</script>

<style lang="scss" scoped>
@import '@/styles/theme';

$transition: cubic-bezier(0.4, 0, 0.2, 1);
$duration: 500ms;
.audio-player {
  user-select: none;
  width: 100%;
  height: 80px;
  background: white;
  position: fixed;
  bottom: 0;
  right: 0;
  white-space: nowrap;
  z-index: 10;
  box-shadow: 0 -2px 8px 4px rgba(0,0,0,0.16);
  display: flex;
  transition: width $duration $transition,
     height $duration $transition,
     left $duration $transition,
     right $duration $transition,
     bottom $duration $transition;

  .artwork img {
    width: 80px;
  }
}

.seek-bar {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  overflow: hidden;
  will-change: height;
  transition: height 280ms;

  .seek-bar__progress {
    cursor: pointer;
  }
}

.player-content {
  width: 100%;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;

  .track-info {
    padding: 0 12px;
  }
  .player-sub-actions {
    padding: 0 12px;
  }
}

.audio-player--hovering {
  .seek-bar {
    height: 6px;
  }
}

.artwork {
  position: relative;
}
.audio-player--floating {
  width: 270px;
  bottom: 24px;
  right: 24px;
  left: auto;
  border-radius: 4px;
  overflow: hidden;

  .track-info {
    opacity: 0;
  }
}

.queue-item--active {
  background-color: map-deep-get($colors, 'deep-orange', 'lighten-5');
}

@media #{map-get($display-breakpoints, 'md-and-down')} {
    .audio-player {
      z-index: 3 !important;
    }
}
</style>

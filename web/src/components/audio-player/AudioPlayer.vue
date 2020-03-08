<template>
    <div :class="classes"
         @mouseenter="hovering = true"
         @mouseleave="hovering = false"
         v-if="track"
    >
      <div class="audio-player__mobile-header"
           v-ripple
           v-if="mobile && !minimized"
           @click="toggleMinimized"
      >
        <v-icon large>remove</v-icon>
      </div>
      <v-hover class="artwork">
        <template v-slot:default="{ hover }">
          <div @click="toggleMinimized">
            <img crossorigin :src="artwork" />
            <v-fade-transition>
              <v-overlay v-if="hover && minimized && !mobile" absolute>
                <v-icon>fullscreen</v-icon>
              </v-overlay>
            </v-fade-transition>
          </div>
        </template>
      </v-hover>
      <div class="player-content">
        <v-expand-x-transition>
          <div class="track-info" v-if="!minimized || mobile">
            <div class="track-info--track-name body-1" @click="onTrackTitleClicked">
              {{ track.title }}
            </div>
            <div class="track-info--track-meta body-2" @click="onReciterNameClicked">
              {{ track.reciter.name }} &bull; {{ track.year }}
            </div>
          </div>
        </v-expand-x-transition>
        <div class="seek-bar">
          <v-progress-linear
            :active="(mobile && !minimized) || duration !== 0"
            v-model="progress"
            color="deep-orange"
            height="8"
            :background-opacity="hovering || mobile ? 0.3 : 0"
            class="seek-bar__progress">
          </v-progress-linear>
          <div class="seek-bar__timestamps">
            <div class="seek-bar__timestamps__current">{{ formattedSeek }}</div>
            <div class="seek-bar__timestamps__duration">{{ formattedDuration }}</div>
          </div>
        </div>
        <div class="player-actions">
          <v-btn
            v-if="!mobile || !minimized"
            icon
            :height="playbackControlSizes.standard.button"
            :width="playbackControlSizes.standard.button"
            @click="previous"
          >
            <v-icon :size="playbackControlSizes.standard.icon">skip_previous</v-icon>
          </v-btn>
          <v-btn icon
                 :height="playbackControlSizes.prominent.button"
                 :width="playbackControlSizes.prominent.button"
                 color="deep-orange"
                 @click="togglePlayState"
          >
            <v-icon v-if="playing" :size="playbackControlSizes.prominent.icon">
              pause_circle_filled
            </v-icon>
            <v-icon v-else :size="playbackControlSizes.prominent.icon">
              play_circle_filled
            </v-icon>
          </v-btn>
          <v-btn
            icon
            v-if="!mobile || !minimized"
            :height="playbackControlSizes.standard.button"
            :width="playbackControlSizes.standard.button"
            @click="next"
            :disabled="!hasNext"
          >
            <v-icon :size="playbackControlSizes.standard.icon">skip_next</v-icon>
          </v-btn>
          <v-menu
            v-if="minimized"
            top
            offset-y
          >
          <template v-slot:activator="{ on }">
              <v-btn
                icon large
                v-on="on"
              >
                <v-icon>more_vert</v-icon>
              </v-btn>
            </template>
            <v-card>
              <v-list>
                <v-list-item @click="goToReciter">Go to reciter</v-list-item>
                <v-list-item @click="goToTrack">Go to track</v-list-item>
                <v-list-item @click="toggleMinimized">Expand player</v-list-item>
              </v-list>
            </v-card>
          </v-menu>
        </div>
        <v-expand-transition>
          <div class="player-sub-actions" v-if="!minimized && !mobile">
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
                <queue-list @change="resetQueueMenu"></queue-list>
              </v-card>
            </v-menu>
            <v-btn @click="toggleMinimized" icon large><v-icon>picture_in_picture_alt</v-icon></v-btn>
          </div>
        </v-expand-transition>
      </div>
      <div class="audio-player__up-next" v-if="mobile && !minimized">
        <h5 class="title px-6">On the Queue</h5>
        <v-expand-transition>
          <queue-list @change="resetQueueMenu"></queue-list>
        </v-expand-transition>
      </div>
    </div>
</template>

<script lang="ts">
/* eslint-disable no-undef */
import { Component, Vue, Watch } from 'vue-property-decorator';
import { Howl } from 'howler';
import * as moment from 'moment';
import QueueList from '@/components/audio-player/QueueList.vue';
import { PlayerState, QueuedTrack, TrackQueue } from '@/store/modules/player';

interface CachedTrackReference {
  queued: QueuedTrack|null;
  index: number|null;
}

@Component({
  components: {
    QueueList,
  },
})
export default class AudioPlayer extends Vue {
  /* Denote whether the user is hovering over the player */
  private hovering = false;
  /* Denote weather the the audio-player is playing */
  private playing = false;
  /* Denote whether the player is "minimized". Default to minimized on Mobile */
  private minimized = false;
  /* Playback engine */
  private howl: Howl|undefined = undefined;
  /* Cache the current playing track to compare */
  private currentTrack: CachedTrackReference = {
    queued: null,
    index: null,
  };
  /* Denote whether the menu for the queue is 'minimized' */
  private queueMenu = false;
  /* Keep a reference to the progress bar interval to clear it when needed. */
  private progressInterval: number|null = null;

  get classes() {
    return {
      'audio-player': true,
      'audio-player--hovering': this.hovering && !this.mobile,
      'audio-player--minimized': this.minimized,
      'audio-player--expanded': !this.minimized,
      'audio-player--mobile': this.mobile,
    };
  }

  get mobile() {
    return this.$vuetify.breakpoint.smAndDown;
  }

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

  get playbackControlSizes() {
    if (this.mobile && !this.minimized) {
      return {
        standard: { button: 48, icon: 40 },
        prominent: { button: 96, icon: 88 },
      };
    }

    return {
      standard: { button: 36, icon: 28 },
      prominent: { button: 48, icon: 40 },
    };
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
    return this.$store.getters['player/progress'];
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
   * Get the duration from the store.
   */
  get duration(): number {
    return this.$store.state.player.duration;
  }

  /**
   * Get the seek from the store
   */
  get seek(): number {
    return this.$store.state.player.seek;
  }

  get formattedSeek() {
    return moment.utc(moment.duration(this.seek, 'seconds').asMilliseconds()).format('m:ss');
  }

  get formattedDuration() {
    return moment.utc(moment.duration(this.duration, 'seconds').asMilliseconds()).format('m:ss');
  }

  mounted() {
    this.minimized = this.mobile;
  }

  /**
   * Check to see weather a track is the current track
   */
  isCurrentTrack(id: string) {
    return (this.currentTrack.queued && this.currentTrack.queued.id === id);
  }

  /**
   * Reset queueMenu back to true to re-render the height of the queue menu
   */
  resetQueueMenu() {
    this.queueMenu = false;
    this.$nextTick(() => this.queueMenu = true);
  }

  onTrackTitleClicked() {
    if (this.mobile && this.minimized) {
      this.toggleMinimized();
      return;
    }
    this.goToTrack();
  }

  onReciterNameClicked() {
    if (this.mobile && this.minimized) {
      this.toggleMinimized();
      return;
    }
    this.goToReciter();
  }

  goToReciter() {
    if (this.mobile && !this.minimized) {
      this.toggleMinimized();
    }

    this.$router.push({
      name: 'reciters.show',
      params: { reciter: this.track.reciter.slug },
    }).catch(() => null);
  }

  goToTrack() {
    if (this.mobile && !this.minimized) {
      this.toggleMinimized();
    }

    this.$router.push({
      name: 'tracks.show',
      params: {
        reciter: this.track.reciter.slug,
        album: this.track.album.year,
        track: this.track.slug,
        trackObject: this.track,
      },
    }).catch(() => null);
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
    this.howl = undefined;
    this.play();
  }

  @Watch('queue')
  onQueueUpdate() {
    this.updateMediaSessionNextHandler();
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
   * Makes the audio player minimized
   */
  toggleMinimized() {
    this.minimized = !this.minimized;

    // Try to limit scrolling when maximized.
    document.documentElement.classList.remove('overflow-y-hidden');
    if (!this.minimized && this.mobile) {
      document.documentElement.classList.add('overflow-y-hidden');
    }
  }

  /**
   * Start playing the current track.
   * If no player is initialized, initialize Howler.
   */
  play() {
    if (!this.howl) {
      this.howl = this.initializeHowler();
    }

    this.howl.play();
    this.updatePlaybackStateOnMediaSession();
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
    this.updatePlaybackStateOnMediaSession();
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
    const seek = 0;
    const duration = 0;

    this.$store.commit('player/UPDATE_TRACK_PROGRESS', { seek, duration });
    this.updatePlaybackStateOnMediaSession();
    if (this.progressInterval !== null) {
      window.clearInterval(this.progressInterval);
    }
  }

  previous() {
    if (this.hasPrevious && this.progress < 2) {
      this.$store.commit('player/PREVIOUS');
      return;
    }

    // Reset the track if the current track is more than 2 percent complete
    // or if there's no previous track..
    if (this.howl) {
      this.howl.seek(0);
    }
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

    const seek = (this.howl.seek() as number);
    const duration = this.howl.duration();
    this.$store.commit('player/UPDATE_TRACK_PROGRESS', { seek, duration });
    this.updateSeekOnMediaSession();
  }

  /**
   * Initialize Howler for playback.
   * Bind event listeners to Howl.
   */
  initializeHowler(): Howl {
    if (this.uri === null) {
      throw new TypeError('Cannot instantiate player: no audio track URI available.');
    }

    const howl = new Howl({
      src: [this.uri],
      html5: true,
    });

    // Register seek binding.
    howl.on('play', () => {
      this.playing = true;
      this.progressInterval = window.setInterval(() => this.updateSeek(), 1000 / 4);
    });

    // Set up media session API once on play.
    howl.once('play', () => {
      this.setMediaSessionMetadata();
    });

    // Register end binding.
    howl.on('end', () => {
      if (this.hasNext) {
        this.next();
      } else {
        this.stop();
      }
    });

    // Register pause binding.
    howl.on('pause', () => {
      this.playing = false;
      if (this.progressInterval) {
        window.clearInterval(this.progressInterval);
      }
    });

    return howl;
  }

  setMediaSessionMetadata() {
    if ('mediaSession' in navigator && this.track) {
      // eslint-disable-next-line @typescript-eslint/ban-ts-ignore
      // @ts-ignore
      (navigator as any).mediaSession.metadata = new MediaMetadata({
        title: this.track.title,
        artist: this.track.reciter.name,
        album: this.track.album.year,
        artwork: [
          { src: this.artwork, sizes: '512x512', type: 'image/png' },
        ],
      });
      (navigator as any).mediaSession.setActionHandler('play', () => this.play());
      (navigator as any).mediaSession.setActionHandler('pause', () => this.pause());
      // navigator.mediaSession.setActionHandler('seekbackward', function() {});
      // navigator.mediaSession.setActionHandler('seekforward', function() {});
      (navigator as any).mediaSession.setActionHandler('previoustrack', () => this.previous());
      this.updateMediaSessionNextHandler();
    }
  }

  updateMediaSessionNextHandler() {
    if (this.hasNext) {
      (navigator as any).mediaSession.setActionHandler('nexttrack', () => this.next());
    }
  }

  updatePlaybackStateOnMediaSession() {
    const nav = (navigator as any);
    if ('mediaSession' in nav && 'playbackState' in nav.mediaSession) {
      let state = 'none';
      if (this.track) {
        state = (this.playing ? 'playing' : 'paused');
      }
      nav.mediaSession.playbackState = state;
    }
  }

  updateSeekOnMediaSession() {
    if ('mediaSession' in navigator && this.track) {
      if (typeof (navigator as any).mediaSession.setPositionState !== 'function') {
        return;
      }

      (navigator as any).mediaSession.setPositionState({
        duration: this.duration,
        position: this.seek,
        playbackRate: 1,
      });
    }
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

    if (this.progressInterval) {
      window.clearInterval(this.progressInterval);
    }
  }
}
</script>

<style lang="scss" scoped>
@import '@/styles/theme';

$transition: cubic-bezier(0.4, 0, 0.2, 1);
$duration: 680ms;

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

  .artwork {
    cursor: pointer;
    img {
      transition: width $duration $transition, max-width $duration $transition;
      max-width: 80px;
    }
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
    flex: 1;
  }
  .player-actions {
    margin: auto;
    justify-content: center;
    display: flex;
    align-items: center;
  }
  .player-sub-actions {
    padding: 0 12px;
    flex: 1;
    display: flex;
    justify-content: flex-end;
  }
}

.audio-player--hovering {
  .seek-bar {
    height: 6px;
  }

  .track-info {
    &--track-name, &--track-meta {
      cursor: pointer;
    }
  }
}

.artwork {
  position: relative;
}
.audio-player--minimized {
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

@media #{map-get($display-breakpoints, 'md-and-down')} {
    .audio-player {
      z-index: 3 !important;
    }
}

.audio-player__mobile-header {
  width: 100%;
  text-align: center;
  padding: 4px;
}

.audio-player--mobile.audio-player--expanded {
  height: 98%;
  border-radius: 16px 16px 0 0;
  overflow-y: auto;
  overflow-x: hidden;
  z-index: 100 !important;
  flex-direction: column;

  .audio-player__mobile-header {
    width: 100%;
    text-align: center;
    padding: 4px;
  }

  .artwork {
    padding: 24px 88px;
    text-align: center;

    img {
      width: 100%;
      max-width: 400px;
    }
  }

  .player-content {
    padding: 24px 48px;
    position: relative;
    display: flex;
    min-height: min-content;
    flex-direction: column;

    .track-info {
      margin-bottom: 36px;
      text-align: center;

      .track-info--track-name {
        font-size: 24px !important;
        margin-bottom: 12px;
      }
    }

    .seek-bar {
      position: relative;
      top: auto;
      left: auto;
      width: 100%;
      height: auto;
      margin-bottom: 24px;

      .seek-bar__timestamps {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: rgba(0, 0, 0, 0.6);
        margin-top: 4px;
      }
    }

    .player-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      padding: 0 24px;
    }
  }
}


@media screen and (orientation:landscape) {
  .audio-player--mobile.audio-player--expanded {
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;

    .audio-player__mobile-header {
      flex: none;
      height: 60px;
    }

    .artwork {
      padding: 24px;
      flex-shrink: 1;
      img {
        width: 220px;
      }
    }
    .player-content {
      flex: 1;
    }
    .audio-player__up-next {
      flex-grow: 1;
    }
  }
}

.audio-player--mobile.audio-player--minimized {
  height: 80px;
  bottom: 0;
  right: 0;
  width: 100%;
  padding-right: 8px;

  .track-info {
    opacity: 1;
  }
}
</style>

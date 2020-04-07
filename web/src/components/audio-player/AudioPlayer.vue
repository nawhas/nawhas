<template>
    <v-sheet
      :class="classes"
      @mouseenter="hovering = true"
      @mouseleave="hovering = false"
      v-if="track"
    >
      <!--
        -- Mobile Header --
        Toggles the minimized/maximized state of the player.
        Only shown on mobile.
      -->
      <div
        class="audio-player__mobile-header"
        v-ripple
        v-if="mobile && !minimized"
        @click="toggleMinimized"
      >
        <v-icon large :color="vibrantTextColor">remove</v-icon>
      </div>

      <!--
        -- Artwork --
        Toggles the minimized/maximized state of the player.
      -->
      <div class="artwork" :style="{ 'background-color': mobile && !minimized ? vibrantBackgroundColor : 'none' }">
        <div @click="toggleMinimized">
          <img crossorigin :src="artwork" :style="{ opacity: mobile && !minimized && currentOverlay ? 0 : 1 }" />
        </div>
        <div class="overlay overlay--lyrics" v-if="mobile && !minimized && currentOverlay === 'lyrics'">
          <lyrics-renderer
            ref="lyrics"
            class="lyrics__renderer"
            v-if="track.lyrics"
            :track="track"
            @highlight:changed="scrollToCurrentLyricsGroup"
          />
        </div>
        <div class="overlay overlay--queue" v-else-if="mobile && !minimized && currentOverlay === 'queue'">
          <!--
            -- Queue --
            Displays what is currently on the queue
            Only Shown on mobile full screen
          -->
          <div class="audio-player__up-next" v-if="mobile && !minimized">
            <v-expand-transition>
              <queue-list :dark="true" @change="resetQueueMenu"></queue-list>
            </v-expand-transition>
          </div>
        </div>
      </div>

      <div class="player-content">
        <!--
          -- Track Title and Metadata --
        -->
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

        <!--
          -- Seek Bar --
        -->
        <div class="seek-bar">
          <v-progress-linear
            :active="(mobile && !minimized) || duration !== 0"
            v-model="progress"
            color="deep-orange"
            height="8"
            :background-opacity="hovering || mobile ? 0.3 : 0"
            class="seek-bar__progress">
          </v-progress-linear>
          <div :class="{'seek-bar__timestamps': true, 'seek-bar__timestamps--disabled': isDark}">
            <div class="seek-bar__timestamps__current">{{ formattedSeek }}</div>
            <div class="seek-bar__timestamps__duration">{{ formattedDuration }}</div>
          </div>
        </div>
        <!--
          -- Player Actions --
        -->
        <div class="player-actions">
          <v-btn
            icon
            v-if="!minimized"
            @click="toggleShuffle"
            :color="shuffled ? 'deep-orange' : 'secondary'"
          >
            <v-icon>shuffle</v-icon>
          </v-btn>
          <v-btn
            v-if="!mobile || !minimized"
            icon
            :height="playbackControlSizes.standard.button"
            :width="playbackControlSizes.standard.button"
            @click="previous"
          >
            <v-icon :size="playbackControlSizes.standard.icon">skip_previous</v-icon>
          </v-btn>
          <v-btn
            icon
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
          <v-btn
            @click="toggleRepeat"
            icon
            v-if="!minimized"
            :color="repeat ? 'deep-orange' : 'secondary'"
          >
            <v-icon v-if="repeat === null || repeat === 'all'">repeat</v-icon>
            <v-icon v-else>repeat_one</v-icon>
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
          <!--
            -- Overflow Menu --
          -->
          <div class="player-sub-actions" v-if="!minimized && !mobile">
            <v-menu
              v-model="queueMenu"
              top
              :nudge-left="300"
              offset-y
              allow-overflow
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

              <!--
                -- Queue --
                Displays what is currently on the queue
                Only shown on desktop and expaned
              -->
              <v-card class="queue-list-menu">
                <v-card-title class="queue-list-menu__title">
                  On the Queue
                </v-card-title>
                <queue-list @change="resetQueueMenu"></queue-list>
              </v-card>
            </v-menu>
            <v-btn @click="toggleMinimized" icon large><v-icon>picture_in_picture_alt</v-icon></v-btn>
          </div>
        </v-expand-transition>
      </div>

      <!--
        -- Action Bar --
        Common actions for the player
        Only Shown on mobile full screen
      -->
      <div
        v-if="mobile && !minimized"
        class="audio-player__bottom-actions"
      >
        <v-btn
          text
          @click="toggleOverlay('lyrics')"
          :class="{'bottom-actions__button': true, 'bottom-action__button--active': currentOverlay === 'lyrics'}"
        >
          <v-icon left>speaker_notes</v-icon>
          Write-Up
        </v-btn>
        <v-btn
          text
          @click="toggleOverlay('queue')"
          :class="{'bottom-actions__button': true, 'bottom-action__button--active': currentOverlay === 'queue'}"
        >
          <v-icon left>queue_music</v-icon>
          Queue
        </v-btn>
      </div>
    </v-sheet>
</template>

<script lang="ts">
/* eslint-disable no-undef */
import { Component, Vue, Watch } from 'vue-property-decorator';
import Vibrant from 'node-vibrant';
import { Howl } from 'howler';
import * as moment from 'moment';
import QueueList from '@/components/audio-player/QueueList.vue';
import LyricsRenderer from '@/components/lyrics/LyricsRenderer.vue';
import {
  PlayerState, QueuedTrack, TrackQueue, RepeatType,
} from '@/store/modules/player';

interface CachedTrackReference {
  queued: QueuedTrack|null;
  index: number|null;
}

@Component({
  components: {
    QueueList,
    LyricsRenderer,
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
  /* Background color for the audio player container */
  private vibrantBackgroundColor = 'rgb(150, 37, 2)';
  /* Text color for the audio player container */
  private vibrantTextColor = '#fff';
  private currentOverlay: null | 'lyrics' | 'queue' = null;

  get isDark() {
    return this.$vuetify.theme.dark;
  }

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
    return this.$store.getters['player/queue'];
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
    return this.store.duration;
  }

  /**
   * Get the seek from the store
   */
  get seek(): number {
    return this.store.seek;
  }

  get shuffled(): boolean {
    return this.store.isShuffled;
  }

  get repeat(): RepeatType {
    return this.store.repeat;
  }

  get formattedSeek() {
    return moment.utc(moment.duration(this.seek, 'seconds').asMilliseconds()).format('m:ss');
  }

  get formattedDuration() {
    return moment.utc(moment.duration(this.duration, 'seconds').asMilliseconds()).format('m:ss');
  }

  toggleOverlay(key) {
    if (this.currentOverlay !== key) {
      this.currentOverlay = key;
      return;
    }
    this.currentOverlay = null;
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

    this.setBackgroundFromImage();

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
   * Toggles the shuffle
   */
  toggleShuffle() {
    this.$store.commit('player/TOGGLE_SHUFFLE');
  }

  /**
   * Toggle the repeat
   */
  toggleRepeat() {
    this.$store.commit('player/TOGGLE_REPEAT');
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
      if (this.repeat === 'one') {
        this.play();
        return;
      }
      if (this.hasNext) {
        this.next();
      } else {
        if (this.repeat === 'all') {
          this.$store.commit('player/SKIP_TO_TRACK', { id: this.queue[0].id });
          return;
        }
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

  setBackgroundFromImage() {
    Vibrant.from(this.artwork)
      .getPalette()
      .then((palette) => {
        const swatch = palette.DarkMuted;
        if (!swatch) {
          return;
        }
        this.vibrantBackgroundColor = swatch.getHex();
        this.vibrantTextColor = swatch.getBodyTextColor();
      });
  }

  scrollToCurrentLyricsGroup(id) {
    if (id === null) {
      return;
    }
    const renderer = (this.$refs.lyrics as Vue);
    this.$nextTick(() => {
      const results = renderer.$el.querySelector('.lyrics__group--highlighted');
      return results && results.scrollIntoView({ block: 'center', behavior: 'smooth' });
    });
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
      transition: width $duration $transition,
        opacity 280ms $transition,
        max-width $duration $transition;
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
    justify-content: space-around;
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
  position: absolute;
  top: 0;
  left: 0;
  z-index: 10;
}

.audio-player--mobile.audio-player--expanded {
  height: 100%;
  border-radius: 16px 16px 0 0;
  overflow-y: hidden;
  overflow-x: hidden;
  z-index: 100 !important;
  flex-direction: column;
  justify-content: space-between;

  .audio-player__mobile-header {
    width: 100%;
    text-align: center;
    padding: 4px;
  }

  .artwork {
    background: rgb(150, 37, 2);
    padding: 68px 88px 48px;
    text-align: center;
    position: relative;

    img {
      width: 100%;
      max-width: 400px;
      border: 4px solid white;
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow-x: hidden;
      overflow-y: hidden;
      z-index: 5;
      padding: 44px 0 12px 0;
      margin: 0;

      .audio-player__up-next {
        height: 100%;
        overflow-y: auto;
        text-align: left;
      }
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

        &--disabled {
          color: white !important;
        }
      }
    }

    .player-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      padding: 0;
    }
  }
  .audio-player__bottom-actions {
    width: 100%;
    margin-bottom: 12px;
    display: flex;
    padding: 15px 30px;
    justify-content: space-between;

    .bottom-action__button--active {
      color: $accent;
    }
  }
}

.queue-list-menu {
  max-height: calc(100vh - 180px);
  position: relative;

  .queue-list-menu__title {
    position: sticky;
    padding: 16px 16px 8px;
    top: 0;
    z-index: 1;
    margin-bottom: -8px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
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


.lyrics__renderer {
  font-size: 32px;
  font-weight: 600;
  padding: 12px 36px;
  overflow-y: auto;
  overflow-x: hidden;
  white-space: normal;
  text-align: left;
  height: 100%;
  width: 100%;
}


.lyrics__renderer ::v-deep .lyrics__plain-text {
  padding: 0 24px;
}

.lyrics__renderer ::v-deep .lyrics__group {
  padding: 8px 0;
  color: rgba(255,255,255, 0.76);


  .lyrics__group__timestamp {
    font-family: 'Roboto Mono', monospace;
    color: rgba(0, 0, 0, 0.5);
    width: 45px;
    margin-right: 16px;
    text-align: right;
    font-size: 14px;
  }

  .lyrics__spacer {
    display: none;
  }

  .lyrics__text {
    display: inline;
  }

  .lyrics__repeat {
    display: inline-block;
    margin-left: 8px;
    margin-bottom: 3px;
    padding: 5px 8px;
    text-align: center;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Roboto Mono', monospace;
    font-weight: 600;
    line-height: 14px;
    border: 1px solid rgba(0,0,0,0.6);
    vertical-align: middle;
  }
}

.lyrics__renderer ::v-deep .lyrics__group {
  color: rgba(255, 255, 255, 0.58);

  &.lyrics__group--highlighted {
    color: white;
  }
  .lyrics__repeat {
    border-color: rgba(255,255,255,0.76);
  }
}
</style>

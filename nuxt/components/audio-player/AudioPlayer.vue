<template>
  <v-sheet
    v-if="track"
    :class="classes"
    @mouseenter="hovering = true"
    @mouseleave="hovering = false"
  >
    <!--
        # Mobile Header
        Toggles the minimized/maximized state of the player.
        Only shown on mobile.
      -->
    <div
      v-if="mobile && !minimized"
      class="audio-player__mobile-header"
      @click="toggleMinimized"
    >
      <v-icon large dark>
        expand_more
      </v-icon>
    </div>

    <!--
        # Artwork
        Toggles the minimized/maximized state of the player.
      -->
    <div class="artwork">
      <div
        :class="{
          'artwork__image': true,
          'artwork__image--overlay': mobile && !minimized && currentOverlay,
          'artwork__image--default': !hasArtwork,
        }"
        @click="toggleMinimized"
      >
        <img crossorigin :src="artwork">
      </div>
      <v-fade-transition>
        <div v-if="mobile && !minimized && currentOverlay === 'lyrics'" class="overlay overlay--lyrics">
          <lyrics-overlay :track="track" />
        </div>
        <div v-else-if="mobile && !minimized && currentOverlay === 'queue'" class="overlay overlay--queue">
          <!--
              # Queue
              Displays what is currently on the queue
              Only Shown on mobile full screen
            -->
          <div v-if="mobile && !minimized" class="audio-player__up-next">
            <queue-list :dark="true" @change="resetQueueMenu" />
          </div>
        </div>
      </v-fade-transition>
    </div>

    <div class="player-content">
      <!--
          # Track Title and Metadata
      -->
      <v-expand-x-transition @before-leave="ignoreExpand">
        <div v-if="!minimized || mobile" class="track-info">
          <div class="track-info--container">
            <div class="track-info--track-name body-1" @click="onTrackTitleClicked">
              {{ track.title }}
            </div>
            <div class="track-info--track-meta body-2" @click="onReciterNameClicked">
              {{ track.reciter.name }} &bull; {{ track.year }}
            </div>
          </div>
          <favorite-track-button
            v-if="!mobile && !minimized"
            :track="track.id"
            class="track-info__favorite"
          />
        </div>
      </v-expand-x-transition>

      <!--
          # Seek Bar
        -->
      <div class="seek-bar">
        <v-progress-linear
          v-model="progress"
          :active="(mobile && !minimized) || duration !== 0"
          color="deep-orange"
          height="8"
          :background-opacity="hovering || mobile ? 0.3 : 0"
          class="seek-bar__progress"
        />
        <div :class="{'seek-bar__timestamps': true, 'seek-bar__timestamps--disabled': isDark}">
          <div class="seek-bar__timestamps__current">
            {{ formattedSeek }}
          </div>
          <div class="seek-bar__timestamps__duration">
            {{ formattedDuration }}
          </div>
        </div>
      </div>
      <!--
          # Player Actions
        -->
      <div class="player-actions">
        <v-btn
          v-if="!minimized"
          icon
          :color="shuffled ? 'deep-orange' : 'secondary'"
          @click="toggleShuffle"
        >
          <v-icon>shuffle</v-icon>
        </v-btn>
        <favorite-track-button
          v-if="mobile && minimized"
          :track="track.id"
        />
        <v-btn
          v-if="!mobile || !minimized"
          icon
          :height="playbackControlSizes.standard.button"
          :width="playbackControlSizes.standard.button"
          @click="previous"
        >
          <v-icon :size="playbackControlSizes.standard.icon">
            skip_previous
          </v-icon>
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
          v-if="!mobile || !minimized"
          icon
          :height="playbackControlSizes.standard.button"
          :width="playbackControlSizes.standard.button"
          :disabled="!hasNext"
          @click="next"
        >
          <v-icon :size="playbackControlSizes.standard.icon">
            skip_next
          </v-icon>
        </v-btn>
        <v-btn
          v-if="!minimized"
          icon
          :color="repeat ? 'deep-orange' : 'secondary'"
          @click="toggleRepeat"
        >
          <v-icon v-if="repeat === null || repeat === 'all'">
            repeat
          </v-icon>
          <v-icon v-else>
            repeat_one
          </v-icon>
        </v-btn>
        <v-menu
          v-if="minimized"
          top
          offset-y
        >
          <template #activator="{ on }">
            <v-btn
              icon
              large
              v-on="on"
            >
              <v-icon>more_vert</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-list>
              <v-list-item @click="goToReciter">
                Go to reciter
              </v-list-item>
              <v-list-item @click="goToTrack">
                Go to track
              </v-list-item>
              <v-list-item @click="onSaveTrack">
                <span v-if="!isTrackSaved">Add to Library</span>
                <span v-else>Remove from Library</span>
              </v-list-item>
              <v-list-item @click="toggleMinimized">
                Expand player
              </v-list-item>
            </v-list>
          </v-card>
        </v-menu>
      </div>
      <v-expand-transition @beforeEnter="ignoreExpand">
        <!--
            # Overflow Menu
          -->
        <div v-if="!minimized && !mobile" class="player-sub-actions">
          <v-menu
            v-model="queueMenu"
            top
            :nudge-left="300"
            offset-y
            allow-overflow
            :close-on-content-click="false"
          >
            <template #activator="{ on }">
              <v-btn
                icon
                large
                v-on="on"
              >
                <v-icon>playlist_play</v-icon>
              </v-btn>
            </template>

            <!--
                # Queue
                Displays what is currently on the queue
                Only shown on desktop and expanded
              -->
            <v-card class="queue-list-menu">
              <v-card-title class="queue-list-menu__title">
                On the Queue
              </v-card-title>
              <queue-list @change="resetQueueMenu" />
            </v-card>
          </v-menu>
          <v-btn icon large @click="toggleMinimized">
            <v-icon>picture_in_picture_alt</v-icon>
          </v-btn>
        </div>
      </v-expand-transition>
    </div>

    <!--
        # Action Bar
        Common actions for the player
        Only Shown on mobile full screen
      -->
    <div
      v-if="mobile && !minimized"
      class="audio-player__bottom-actions"
    >
      <div class="bottom-actions__left">
        <v-btn
          text
          :disabled="!track.lyrics"
          :class="{'bottom-actions__button': true, 'bottom-action__button--active': currentOverlay === 'lyrics'}"
          @click="toggleOverlay('lyrics')"
        >
          <v-icon left>
            speaker_notes
          </v-icon>
          Write-Up
        </v-btn>
      </div>
      <div class="bottom-actions__center">
        <favorite-track-button
          class="bottom-actions__button"
          :track="track.id"
        />
      </div>
      <div class="bottom-actions__right">
        <v-btn
          text
          :class="{'bottom-actions__button': true, 'bottom-action__button--active': currentOverlay === 'queue'}"
          @click="toggleOverlay('queue')"
        >
          <v-icon left>
            queue_music
          </v-icon>
          Queue
        </v-btn>
      </div>
    </div>
  </v-sheet>
</template>

<script lang="ts">

import { Component, Vue, Watch } from 'nuxt-property-decorator';
import { Howl } from 'howler';
import * as moment from 'moment';
import QueueList from '@/components/audio-player/QueueList.vue';
import LyricsRenderer from '@/components/lyrics/LyricsRenderer.vue';
import LyricsOverlay from '@/components/audio-player/LyricsOverlay.vue';
import {
  PlayerState, QueuedTrack, TrackQueue, RepeatType,
} from '@/store/player';
import { getAlbumArtwork } from '@/entities/album';
import { getReciterUri } from '@/entities/reciter';
import { getTrackUri } from '@/entities/track';
import FavoriteTrackButton from '@/components/tracks/FavoriteTrackButton.vue';

interface CachedTrackReference {
  queued: QueuedTrack|null;
  index: number|null;
}

@Component({
  components: {
    FavoriteTrackButton,
    QueueList,
    LyricsRenderer,
    LyricsOverlay,
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

  get hasArtwork() {
    return this.track && this.track.album.artwork;
  }

  get artwork(): string {
    if (!this.hasArtwork) {
      return getAlbumArtwork(null);
    }

    return getAlbumArtwork(this.track.album);
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

  get isTrackSaved() {
    return this.$store.getters['library/isTrackSaved'](this.track.id);
  }

  ignoreExpand() {
    return null;
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
    this.$nextTick(() => {
      this.queueMenu = true;
    });
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
    this.$router.push(getReciterUri(this.track.reciter));
  }

  goToTrack() {
    if (this.mobile && !this.minimized) {
      this.toggleMinimized();
    }

    this.$router.push(getTrackUri(this.track));
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

    this.updateOverlay();

    this.stop();
    this.howl = undefined;
    this.play();
  }

  @Watch('queue')
  onQueueUpdate() {
    this.updateMediaSessionNextHandler();
  }

  updateOverlay() {
    if (this.currentOverlay === 'lyrics' && !this.track.lyrics) {
      this.currentOverlay = null;
    }
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
    if (!this.track) {
      return;
    }
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
    if (navigator.mediaSession !== undefined && navigator.mediaSession && this.track) {
      navigator.mediaSession.metadata = new MediaMetadata({
        title: this.track.title,
        artist: this.track.reciter.name,
        album: this.track.album.year,
        artwork: [
          { src: this.artwork, sizes: '512x512', type: 'image/png' },
        ],
      });
      navigator.mediaSession.setActionHandler('play', () => this.play());
      navigator.mediaSession.setActionHandler('pause', () => this.pause());
      // navigator.mediaSession.setActionHandler('seekbackward', function() {});
      // navigator.mediaSession.setActionHandler('seekforward', function() {});
      navigator.mediaSession.setActionHandler('previoustrack', () => this.previous());
      this.updateMediaSessionNextHandler();
    }
  }

  updateMediaSessionNextHandler() {
    if (this.hasNext && navigator.mediaSession !== undefined) {
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
    if (navigator.mediaSession && this.track) {
      if (typeof (navigator.mediaSession as any).setPositionState !== 'function') {
        return;
      }

      if (!Number.isFinite(this.seek)) {
        return;
      }

      (navigator.mediaSession as any).setPositionState({
        duration: this.duration,
        position: this.seek,
        playbackRate: 1,
      });
    }
  }

  onSaveTrack() {
    if (!this.track) {
      return;
    }
    if (this.isTrackSaved) {
      this.$store.dispatch('library/removeTrack', {
        ids: [this.track.id],
      });
    } else {
      this.$store.dispatch('library/saveTrack', {
        ids: [this.track.id],
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
@import '~assets/theme';

$transition: cubic-bezier(0.4, 0, 0.2, 1);
$duration: 580ms;

.audio-player {
  user-select: none;
  width: 100%;
  height: 80px;
  position: fixed;
  bottom: 0;
  right: 0;
  white-space: nowrap;
  z-index: 500;
  box-shadow: 0 -2px 8px 4px rgba(0,0,0,0.16);
  display: flex;
  transition: width $duration $transition,
     height $duration $transition,
     left $duration $transition,
     right $duration $transition,
     bottom $duration $transition;

  .artwork {
    cursor: pointer;
    position: relative;
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
  position: relative;
  display: flex;
  flex: 1 1 100%;
  align-items: center;
  justify-content: space-between;

  .track-info {
    padding: 0 12px;
    flex: 1;
    display: flex;
    flex-direction: row;
    align-items: center;
    overflow: hidden;
    &--container {
      display: flex;
      flex-direction: column;
    }
    &__favorite {
      margin-left: 12px;
    }
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
    // Show below nav drawer, but above edit dialogs.
    z-index: 250 !important;
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
  overflow-x: hidden;
  z-index: 100 !important;
  flex-direction: column;
  justify-content: space-between;

  .audio-player__mobile-header {
    width: 100%;
    text-align: center;
    padding: 4px;
    background: linear-gradient(to bottom, rgba(0,0,0,0.75), rgba(0,0,0,0));
  }

  .artwork {
    padding: 60px 0 48px;
    text-align: center;
    position: relative;
    height: 100vw;
    width: 100vw;
    flex: none;

    .artwork__image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;

      img {
        width: 100%;
        height: 100%;
        max-width: initial;
        @include transition(filter 280ms, transform 280ms);
      }

      &.artwork__image--overlay img {
        filter: blur(15px);
        transform: scale(1.3);
      }

      &.artwork__image--default {
        background: rgb(150, 37, 2);
      }
    }

    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.55);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow-x: hidden;
      overflow-y: hidden;
      z-index: 5;
      padding: 44px 0 0 0;
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
    padding: 15px 20px;

    .bottom-actions__left,
    .bottom-actions__center,
    .bottom-actions__right {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .bottom-actions__left {
      justify-content: flex-start;
    }

    .bottom-actions__right {
      justify-content: flex-end;
    }

    .bottom-action__button--active {
      color: $accent;
    }
  }
}

.queue-list-menu {
  max-height: calc(100vh - 180px);
  position: relative;
  overflow-y: auto;
  overflow-x: hidden;

  .queue-list-menu__title {
    position: sticky;
    padding: 16px 16px 8px;
    top: 0;
    z-index: 1;
    margin-bottom: -8px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    background: rgba(255, 255, 255, 0.86);
  }

  &.theme--dark .queue-list-menu__title {
    background: rgba(30, 30, 30, 0.86);
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

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
            <div class="track-info--track-name body-1">
              {{ track.title }}
            </div>
            <div class="track-info--track-meta body-2">
              {{ track.reciter.name }} &bull; {{ track.year }}
            </div>
          </div>
        </v-expand-transition>
        <div class="player-actions">
          <v-btn icon large disabled><v-icon>skip_previous</v-icon></v-btn>
          <v-btn icon x-large color="deep-orange" @click="togglePlayState">
            <v-icon v-if="playing">pause_circle_filled</v-icon>
            <v-icon v-else>play_circle_filled</v-icon>
          </v-btn>
          <v-btn icon large disabled><v-icon>skip_next</v-icon></v-btn>
        </div>
        <v-expand-transition>
          <div class="player-sub-actions">
            <v-btn v-if="!floating" icon large><v-icon>playlist_play</v-icon></v-btn>
            <v-btn v-if="!floating" @click="toggleFloating" icon large><v-icon>picture_in_picture_alt</v-icon></v-btn>
            <v-btn icon><v-icon>more_vert</v-icon></v-btn>
          </div>
        </v-expand-transition>
      </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue, Watch } from 'vue-property-decorator';
import { Howl } from 'howler';

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

  /**
   * Increase the height of the seek bar when hovering
   * for easier usability.
   */
  get seekBarHeight() {
    return this.hovering ? 10 : 4;
  }

  /**
   * Gets the current track from the player store
   */
  get track() {
    return this.$store.state.player.track;
  }

  /**
   * Gets the current queue from the player store
   */
  get queue() {
    return this.$store.state.player.queue;
  }

  @Watch('track')
  onTrackUpdate() {
    this.stop();
    this.howl = null;
    this.play();
  }

  get artwork() {
    if (!this.track || !this.track.album.artwork) {
      return '/img/default-album-image.png';
    }

    return this.track.album.artwork;
  }

  get uri() {
    if (!this.track) {
      return null;
    }

    return this.track.media.data[0].uri;
  }

  /**
   * Update the progress bar with the current playback status.
  */
  get progress() {
    if (!this.seek || !this.duration) {
      return 0;
    }
    return (this.seek / this.duration) * 100;
  }

  /**
   * When progress bar is clicked, update Howl to seek to the
   * given position in the audio track.
   */
  set progress(progress) {
    if (!this.howl) {
      return;
    }
    this.howl.seek((progress / 100) * this.duration);
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
   * Tells Howler to start playing the audio file
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
      this.playing = false;
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
}
</script>

<style lang="scss" scoped>
@import '~vuetify/src/styles/styles';
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

@media #{map-get($display-breakpoints, 'md-and-down')} {
    .audio-player {
      z-index: 3 !important;
    }
}
</style>

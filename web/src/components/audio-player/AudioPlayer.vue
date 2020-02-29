<template>
    <div :class="{ 'audio-player': true, 'audio-player--hovering': hovering }"
         @mouseenter="hovering = true"
         @mouseleave="hovering = false"
    >
      <div class="artwork">
        <img src="/img/default-album-image.png" />
      </div>
      <div class="player-content">
        <div class="seek-bar">
          <v-progress-linear
            v-model="progress"
            color="deep-orange"
            height="8"
            :background-opacity="hovering ? 0.3 : 0"
            class="seek-bar--bar">
          </v-progress-linear>
        </div>
        <div class="track-info">
          <div class="track-info--track-name body-1">
            Chotey Hazrat
          </div>
          <div class="track-info--track-meta body-2">
            Nadeem Sarwar &bull; 2011
          </div>
        </div>
        <div class="player-actions">
          <v-btn icon large disabled><v-icon>skip_previous</v-icon></v-btn>
          <v-btn icon x-large color="deep-orange" @click="togglePlayState">
            <v-icon v-if="playing">pause_circle_filled</v-icon>
            <v-icon v-else>play_circle_filled</v-icon>
          </v-btn>
          <v-btn icon large disabled><v-icon>skip_next</v-icon></v-btn>
        </div>
        <div class="player-sub-actions">
          <v-btn icon large><v-icon>playlist_play</v-icon></v-btn>
          <v-btn icon large><v-icon>picture_in_picture_alt</v-icon></v-btn>
        </div>
      </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import { Howl } from 'howler';

@Component
export default class AudioPlayer extends Vue {
  private seek = 0;
  private duration = 0;
  private hovering = false;
  private playing = false;
  private uri = 'https://s3.us-east-2.amazonaws.com/staging.nawhas/reciters/hassan-sadiq/albums/2004/tracks/tu-rut-na-roia-ker.mp3';
  private howl: Howl|undefined = null;

  get seekBarHeight() {
    return this.hovering ? 10 : 4;
  }

  get progress() {
    if (!this.seek || !this.duration) {
      return 0;
    }
    return (this.seek / this.duration) * 100;
  }
  set progress(progress) {
    if (!this.howl) {
      return;
    }
    this.howl.seek((progress / 100) * this.duration);
  }

  togglePlayState() {
    if (this.playing) {
      this.pause();
    } else {
      this.play();
    }
  }

  play() {
    if (!this.howl) {
      this.initializeHowler();
    }

    this.howl.play();
    this.playing = true;
  }

  pause() {
    if (!this.howl) {
      return;
    }

    this.howl.pause();
    this.playing = false;
  }

  updateSeek() {
    if (!this.howl) {
      return;
    }

    this.seek = this.howl.seek();
    this.duration = this.howl.duration();
  }

  initializeHowler() {
    this.howl = new Howl({
      src: [this.uri],
    });

    // Register seek binding.
    this.howl.on('play', () => {
      window.setInterval(() => this.updateSeek(), 1000 / 4);
    });
  }
}
</script>

<style lang="scss" scoped>
@import '~vuetify/src/styles/styles';

.audio-player {
  width: 100%;
  height: 80px;
  background: white;
  position: fixed;
  bottom: 0;
  left: 0;
  z-index: 10;
  box-shadow: 0 -2px 8px 4px rgba(0,0,0,0.16);
  display: flex;


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
  cursor: pointer;
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

@media #{map-get($display-breakpoints, 'md-and-down')} {
    .audio-player {
      z-index: 3 !important;
    }
}
</style>

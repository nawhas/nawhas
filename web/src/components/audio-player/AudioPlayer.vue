<template>
    <div class="audio-player">
      <div class="artwork">
        <img src="/img/default-album-image.png" />
      </div>
      <div class="player-content">
        <div class="seek-bar">
          <v-slider
            v-model="currentSeek"
            :min="startTime"
            :max="endTime"
            hide-details>
          </v-slider>
          <div class="current-time">
            {{ currentSeek }}
          </div>
          <v-spacer></v-spacer>
          <div class="end-time">
            {{ endTime }}
          </div>
        </div>
      </div>
    </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class AudioPlayer extends Vue {
  public currentSeek = 0;
  public startTime = 0;
  public endTime = 360;
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

    .player-content {
      width: 100%;
    }
}
@media #{map-get($display-breakpoints, 'md-and-down')} {
    .audio-player {
      z-index: 3 !important;
    }
}
</style>
